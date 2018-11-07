<?php

namespace App\Models;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;
use Schema;
use DB;

//class BaseModel extends Model
trait BaseModelTrait
{

    /**
    * Example Request Fromat
    * [
          'query' =>[
             'filters' => [
                [
                'field'=> "lastname",
                'operators'=> 'ncontains',
                'value1'=>'a'
                ]
             ],
             'model'=>[
                "id",
                "firstname",
                "lastname"
              ],
             'page'=>[
                 'pageno'=>1,
                 'pageSize'=>10
              ],
              'sorts'=>[
                [
                'field'=>'id',
                'order'=>'DESC'
                ]
              ]
          ],
      ];
      */
    public function scopefilter($q, $params)
    {
        if (!isset($params["query"]) || empty($params["query"])) {
            return $q;
        }

        $q = (isset($params["query"]["model"]) && !empty($params["query"]["model"])) == true ? $q->select($params["query"]["model"]) : $q->select();

        $request_data = $params;
        $valid_operators = [
            'gt' => '<',
            'lt' => '>',
            'eq' => '=',
            'neq' => '<>',
            'gte' => '>=',
            'lte' => '<=',
            'startwith' => [
                'op' => 'LIKE',
                'value' => '%s%%'
            ],
            'endwith' => [
                'op' => 'LIKE',
                'value' => '%%%s'
            ],
            'contains' => [
                'op' => 'LIKE',
                'value' => '%%%s%%'
            ],
            'between' => 'between',
            'nstartwith' => [
                'op' => 'NOT LIKE',
                'value' => '%s%%'
            ],
            'nendwith' => [
                'op' => 'NOT LIKE',
                'value' => '%%%s'
            ],
            'ncontains' => [
                'op' => 'NOT LIKE',
                'value' => '%%%s%%'
            ],
            'empty' => 'nullorempty',
        ];

        $valid_sorting_order = [
            "ASC",
            "DESC",
        ];

        if (isset($request_data["query"]["filters"]) && !empty($request_data["query"]["filters"])) {
            foreach ($request_data["query"]["filters"] as $filter) {
                $requested_operator = $filter['operators'];

                if ($requested_operator == "between" && isset($filter["value1"]) && isset($filter["value2"])) {
                    $q->whereBetween($filter['field'], [$filter['value1'], $filter['value2']]);
                    continue;
                }

                if ($requested_operator == "empty") {
                    $q->where($filter['field'], '=', '')->orWhereNull($filter['field']);
                    continue;
                }

                if (isset($filter["value1"]) && $requested_operator != "between" && $requested_operator != "empty") {
                    if (isset($valid_operators[$requested_operator]) && !is_array($valid_operators[$requested_operator])) {
                        $q->where($filter['field'], $valid_operators[$requested_operator], $filter['value1']);
                    } elseif (is_array($valid_operators[$requested_operator])) {
                        $q->where($filter['field'], $valid_operators[$requested_operator]["op"], sprintf($valid_operators[$requested_operator]["value"], $filter['value1']));
                    }
                }
            }
        }

        if (isset($request_data["query"]["sorts"])) {
            foreach ($request_data["query"]["sorts"] as $sort) {
                if (in_array($sort["order"], $valid_sorting_order)) {
                    $q->orderBy($sort["field"], $sort["order"]);
                } else {
                    continue;
                }
            }
        }

        if (isset($request_data["query"]['page'])) {
            $take = $request_data["query"]['page']['pageSize'];
            if ($request_data["query"]['page']['pageno'] > 1) {
                $skip = ($request_data["query"]['page']['pageno'] * $request_data["query"]['page']['pageSize']) - ($request_data["query"]['page']['pageSize']);
                $q->skip($skip);
            }
            $q->take($take);
        } else {
            $q->take(20);
        }

        return $q;
    }

    public function handleValidatation($rules = [], $messages = [])
    {
        $rules = $rules ?: $this->getValidationRules();
        $messages = $messages ?: $this->validationMessages;

        $validator = \Validator::make(\Request::all(), $rules, $messages);

        if ($validator->fails()) {
            //throw new ValidationException($validator->errors()->messages());
            return $validator->errors()->messages();
        }
    }
} //class
