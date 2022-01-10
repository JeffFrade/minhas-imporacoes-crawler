<?php

namespace App\Core\Support;

abstract class BaseException extends \Exception
{
    protected function defaultEmptyMessage(string $content)
    {
        return sprintf('Não foram encontrados(as) %s com os critérios informados.', $content);
    }
}
