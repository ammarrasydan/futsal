<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SSP extends Controller
{
    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    public static function data_output($columns, $data)
    {

        return $data;

        $out = array();

        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    if (empty($column['data'])) {
                        $row[$column['dt']] = $column['formatter']($data[$i]);
                    } else {
                        $row[$column['dt']] = $column['formatter']($data[$i][$column['data']], $data[$i]);
                    }
                } else {
                    if (!empty($column['data'])) {
                        $row[$column['dt']] = $data[$i][$columns[$j]['data']];
                    } else {
                        $row[$column['dt']] = "";
                    }
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    public static function limit($request, $columns)
    {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            // $limit = "OFFSET " . intval($request['start']) . " ROWS FETCH NEXT " . intval($request['length']) . " ROWS ONLY ";
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }

        return $limit;
    }


    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    public static function order($request, $columns)
    {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $column = $columns[$columnIdx];

                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' : 'DESC';

                    $orderBy[] = $column['data'] . ' ' . $dir;
                }
            }

            if (count($orderBy)) {
                $order = 'ORDER BY ' . implode(', ', $orderBy);
            }
        }

        return $order;
    }


    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    public static function filter($request, $columns, &$bindings)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck($columns, 'dt');

        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['searchable'] == 'true') {
                    if (!empty($column['data'])) {
                        $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                        $globalSearch[] = "`" . $column['data'] . "` LIKE " . $binding;
                    }
                }
            }
        }

        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                $str = $requestColumn['search']['value'];

                if (
                    $requestColumn['searchable'] == 'true' &&
                    $str != ''
                ) {
                    if (!empty($column['data'])) {
                        $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                        $columnSearch[] = "`" . $column['data'] . "` LIKE " . $binding;
                    }
                }
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }

        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) : $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }

        return $where;
    }

    public static function complex($request, $fields = '', $table, $primaryKey, $where = null, $wherevalue = null, $order = null)
    {
        $columns = $request['columns'];

        // Build the SQL query string from the request
        $limit = self::limit($request, $columns);

        if ($order) { } else {
            $order = self::order($request, $columns);
        }

        if ($fields == '') {
            $sql_select = "SELECT " . implode(", ", self::pluck($columns, 'data')) . " FROM " . $table . " " . $where . " " . $order . " " . $limit;
        } else {
            $sql_select = "SELECT " . $fields . " FROM " . $table . " " . $where . " " . $order . " " . $limit;
        }

        $data = DB::select($sql_select, $wherevalue);

        $recordsFiltered = 0;
        $resFilterLength = DB::select("SELECT COUNT(" . $primaryKey . ") as recordsFiltered
		FROM   " . $table . " " . $where, $wherevalue);
        $numrow = count($resFilterLength);

        if ($numrow > 0) {
            foreach ($resFilterLength as $row) {
                $recordsFiltered = $row->recordsFiltered;
            }
        }

        $recordsTotal = 0;
        $resTotalLength = DB::select("SELECT COUNT(" . $primaryKey . ") as recordsTotal
		FROM  " . $table, $wherevalue);
        $numrow = count($resTotalLength);

        if ($numrow > 0) {
            foreach ($resTotalLength as $row) {
                $recordsTotal = $row->recordsTotal;
            }
        }

        /*
		 * Output
		 */
        return array(
            "draw"            => isset($request['draw']) ?
                intval($request['draw']) : 0,
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => self::data_output($columns, $data),
            "rawsqlselect" => $sql_select,
            "rawsqlselectwherevalue" => $wherevalue,
            "myorder" => $order,
            "myreqorder" => $request['order'],
            "myrequest" => $request,
            "mycolumns" => $columns,
            "mycolumnspluck" => self::pluck($columns, 'dt')
        );
    }



    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    public static function bind(&$a, $val, $type)
    {
        $key = ':binding_' . count($a);

        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );

        return $key;
    }


    /**
     * Pull a particular property from each assoc. array in a numeric array, 
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    public static function pluck($a, $prop)
    {
        $out = array();

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            if (empty($a[$i][$prop])) {
                continue;
            }
            //removing the $out array index confuses the filter method in doing proper binding,
            //adding it ensures that the array data are mapped correctly
            $out[$i] = $a[$i][$prop];
        }

        return $out;
    }


    /**
     * Return a string from an array or a string
     *
     * @param  array|string $a Array to join
     * @param  string $join Glue for the concatenation
     * @return string Joined string
     */
    public static function _flatten($a, $join = ' AND ')
    {
        if (!$a) {
            return '';
        } else if ($a && is_array($a)) {
            return implode($join, $a);
        }
        return $a;
    }
}
