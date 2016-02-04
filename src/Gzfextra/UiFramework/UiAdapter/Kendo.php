<?php
namespace Gzfextra\UiFramework\UiAdapter;

use Gzfextra\Stdlib\OptionsTrait;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Http\Request;
use Zend\Stdlib\InitializableInterface;
use Zend\View\Model\JsonModel;

/**
 * Class Kendo
 *
 * @author Moln Xie
 */
class Kendo implements UiAdapterInterface, InitializableInterface
{
    use OptionsTrait;

    protected $filter, $sort, $pageSizes = [15, 20, 50, 100];

    /** @var  Request */
    protected $request;

    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Init an object
     *
     * @return void
     */
    public function init()
    {
        $this->filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : [];
        $this->sort   = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : [];
    }

    /**
     * @return int[]
     */
    public function getPageSizes()
    {
        return $this->pageSizes;
    }

    /**
     * @param mixed $pageSizes
     * @return $this
     */
    public function setPageSizes($pageSizes)
    {
        $this->pageSizes = $pageSizes;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    private function parseFilters($filterGroup, $fieldMap)
    {
        $filters = $filterGroup['filters'];
        $logic   = $filterGroup['logic'];

        $predicate = new Predicate();
        foreach ($filters as $filter) {
            if (isset($filter['filters'])) {
                if (count($filter['filters']) == 0) continue;
                $predicate->addPredicate($this->parseFilters($filter, $fieldMap));
            } else {
                $this->addWhere($predicate, $filter, $fieldMap);
            }
            $predicate->$logic;
        }

        return $predicate;
    }

    /**
     * @param array $fieldMap
     *
     * @return Predicate
     */
    public function filter($fieldMap = array())
    {
        if (empty($this->filter['filters'])) {
            return array();
        }

        $where = new Predicate();
        $where->addPredicate($this->parseFilters($this->filter, $fieldMap));

        return $where;
    }

    public function sort()
    {
        if (empty($this->sort)) {
            return array();
        }

        $order = array();
        foreach ($this->sort as $sort) {
            $order[$sort['field']] = $sort['dir'];
        }
        return $order;
    }

    public function result($data, $total = null, array $dataTypes = null)
    {
        if ($dataTypes) {
            $functions = array(
                'boolval' => function ($val) {
                    return (bool)$val;
                }
            );
            foreach ($data as &$row) {
                foreach ($dataTypes as $key => $type) {
                    if (is_callable($type)) {
                        $row[$key] = $type($row[$key], $row);
                    } else if (isset($functions[$type])) {
                        $row[$key] = $functions[$type]($row[$key]);
                    } else {
                        throw new \InvalidArgumentException("错误参数类型($type)");
                    }
                }
            }
        }

        $result = array('data' => $data);
        if ($total) {
            $result['total'] = $total;
        }
        return new JsonModel($result);
    }

    public function errors($messages)
    {
        return new JsonModel(array('errors' => $messages));
    }

    protected function addWhere(Predicate $where, $filter, $fieldMap = array())
    {
        $operatorMap = array(
            'eq'         => 'equalTo',
            'neq'        => 'notEqualTo',
            'lt'         => 'lessThan',
            'lte'        => 'lessThanOrEqualTo',
            'gt'         => 'greaterThan',
            'gte'        => 'greaterThanOrEqualTo',
            'startswith' => 'like',
            'endswith'   => 'like',
            'contains'   => 'like',
            'isnull'     => 'isNull',
        );

        if (!isset($operatorMap[$filter['operator']])) {
            return;
        }

        if (strpos($filter['field'], 'date') !== false
            || strpos($filter['field'], 'time') !== false
        ) {
            $filter['value'] = preg_replace('/\(.*\)$/', '', $filter['value']);
            if ($filter['operator'] == 'startswith') {
                $filter['value'] = date('Y-m-d', strtotime($filter['value']));
            } else {
                $filter['value'] = date('Y-m-d H:i:s', strtotime($filter['value']));
            }
        }

        if (isset($fieldMap[$filter['field']])) {
            if (is_string($fieldMap[$filter['field']])) {
                $filter['field'] = $fieldMap[$filter['field']];
            } else if (is_callable($fieldMap[$filter['field']])) {
                $filter = new \ArrayObject($filter);
                call_user_func($fieldMap[$filter['field']], $filter);
            }
        }

        if (in_array($filter['operator'], ['startswith', 'endswith', 'contains'])) {
            $filter['value'] = str_replace(['_', '%'], ['\\_', '\\%'], $filter['value']);
        }

        switch ($filter['operator']) {
            case 'startswith':
                $filter['value'] .= '%';
                break;
            case 'endswith':
                $filter['value'] = '%' . $filter['value'];
                break;
            case 'contains':
                $filter['value'] = '%' . $filter['value'] . '%';
                break;
            default:
                break;
        }

        $where->{$operatorMap[$filter['operator']]}($filter['field'], $filter['value']);
    }

    public function page()
    {
        $pageSize = current($this->getPageSizes());
        if (in_array((int)$this->request->getPost('pageSize'), $this->getPageSizes())) {
            $pageSize = (int)$this->request->getPost('pageSize');
        }
        return [
            'take'     => (int)$this->request->getPost('take'),
            'page'     => (int)$this->request->getPost('page', 1),
            'pageSize' => $pageSize,
        ];
    }
}