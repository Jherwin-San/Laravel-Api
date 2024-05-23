<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{
    protected $allowedParams = [

    ];

    protected $columnMap = [

    ];

    protected $operatorMap = [

    ];

    // Transform the request parameters into Eloquent query constraints
    public function transform(Request $request)
    {
        $eloQuery = [];

        foreach ($this->allowedParams as $param => $operators) {
            // Check if the parameter is present in the request
            if (!$request->has($param)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;
            $queryValue = $request->input($param);

            foreach ($operators as $operator) {
                // Check if the specific operator is present for the parameter
                if (isset($queryValue[$operator])) {
                    $value = $queryValue[$operator];
                    if ($value === 'null') {
                        if ($operator === 'eq') {
                            $eloQuery[] = [$column, 'isNull'];
                        } elseif ($operator === 'ne') {
                            $eloQuery[] = [$column, 'isNotNull'];
                        }
                    } else {
                        $eloQuery[] = [$column, $this->operatorMap[$operator], $value];
                    }
                }
            }
        }

        return $eloQuery;
    }
}
