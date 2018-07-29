<?php

namespace Nadia\Bundle\PaginatorBundle\Input;

/**
 * Class InputFactory
 */
class InputFactory
{
    /**
     * Generate an Input instance
     *
     * @param array $options
     *
     * @return Input
     */
    public function factory(array $options)
    {
        /** @var QueryParameterDefinition $queryParamDef */
        $queryParamDef = $options['queryParams'];
        $sessionKey = $options['sessionKey'];
        $params = array_merge($_GET, $_POST);
        $clear = array_key_exists($queryParamDef->clear, $params);
        $filter = $search = [];
        $sort = null;
        $limit = $options['defaultLimit'];
        $page = 1;

        if (!$clear) {
            if (isset($_SESSION) && $options['sessionEnabled']) {
                $states = array_key_exists($sessionKey, $_SESSION) ? $_SESSION[$sessionKey] : [];
                $states = (is_array($states) && !$clear) ? $states : [];
            } else {
                $states = [];
            }

            foreach (['filter', 'search'] as $field) {
                $key = $queryParamDef->{$field};
                $value = array_key_exists($key, $states) ? $states[$key] : [];
                $value = array_key_exists($key, $params) ? $params[$key] : $value;
                $$field = is_array($value) ? $value : [];
            }

            $sort = array_key_exists($queryParamDef->sort, $states) ? $states[$queryParamDef->sort] : $sort;
            $sort = array_key_exists($queryParamDef->sort, $params) ? $params[$queryParamDef->sort] : $sort;
            $limit = (int) (array_key_exists($queryParamDef->limit, $states) ? $states[$queryParamDef->limit] : $limit);
            $limit = (int) (array_key_exists($queryParamDef->limit, $params) ? $params[$queryParamDef->limit] : $limit);
            $page = (int) (array_key_exists($queryParamDef->page, $params) ? $params[$queryParamDef->page] : $page);
        }

        if (isset($_SESSION) && $options['sessionEnabled']) {
            $_SESSION[$sessionKey] = [
                $queryParamDef->filter => $filter,
                $queryParamDef->search => $search,
                $queryParamDef->sort => $sort,
                $queryParamDef->limit => $limit,
            ];
        }

        return new Input($filter, $search, $sort, $page, $limit);
    }
}
