<?php

namespace Gzfextra\Mvc\EasyMvc\Protocol;

interface ProtocolInterface
{
    public function __construct($serviceName);

    public function handle();
}