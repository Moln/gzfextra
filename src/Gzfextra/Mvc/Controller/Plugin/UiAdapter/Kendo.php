<?php
namespace Gzfextra\Mvc\Controller\Plugin\UiAdapter;

use Zend\Db\Sql\Where;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;

/**
 * Class Kendo
 * @package Platform\Mvc\Controller\Plugin
 * @author Moln Xie
 * @version $Id$
 */
class Kendo implements UiAdapterInterface
{
    protected $filter, $sort;

    public function __construct(Request $request)
    {
        $this->filter = $request->getPost('filter');
        $this->sort   = $request->getPost('sort');
    }

    /**
     * @param array $fieldMap
     *
     * @return Where
     */
    public function filter($fieldMap = array())
    {
        if (empty($this->filter['filters'])) {
            return array();
        }

        if (empty($this->filter['filters'][0]['filters'])) {
            $filter       = $this->filter;
            $this->filter = array('filters' => array($filter));
        }

        $where = new Where();

        foreach ($this->filter['filters'] as $filters) {
            $logic = isset($filters['logic']) && $filters['logic'] == 'or' ? 'or' : 'and';

            $filter = array_shift($filters['filters']);

            $this->addWhere($where, $filter, $fieldMap);
            if (!empty($filters['filters'])) {
                $filter = array_shift($filters['filters']);
                $where->{$logic};
                $this->addWhere($where, $filter, $fieldMap);
            }
            $where->and;
        }

        return $where;
    }

    public function sort()
    {
        if (empty($this->sort)) {
            return array();
        }

        $order = array();
        foreach ($this->sort as $sort) {
            $order[] = $sort['field'] . ' ' . $sort['dir'];
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

    protected function addWhere(Where $where, $filter, $fieldMap = array())
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

        if (isset($fieldMap[$filter['field']])) {
            if (is_string($fieldMap[$filter['field']])) {
                $filter['field'] = $fieldMap[$filter['field']];
            } else if (is_callable($fieldMap[$filter['field']])) {
                $filter = new \ArrayObject($filter);
                call_user_func($fieldMap[$filter['field']], $filter);
            }
        }
        $where->{$operatorMap[$filter['operator']]}($filter['field'], $filter['value']);
    }
}