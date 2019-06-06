<?php

namespace App\Traits;

trait UpdateSort
{
    public function commonSort($model, $action, $id, $sort, $scopeNodeName = null, $scopeNodeValue = null, $sortType = 'sort', $pid = null)
    {
        $builder = $model::query()
            ->when(!empty($scopeNodeName) && !empty($scopeNodeValue), function ($query) use ($scopeNodeName, $scopeNodeValue) {
                return $query->where($scopeNodeName, $scopeNodeValue);
            })
            ->when($pid, function ($query) use ($pid) {
                return $query->where('pid', $pid);
            });

        if ('up' == $action) {
            $prevItem = $builder->where($sortType, '>', $sort)
                ->orderBy($sortType, 'asc')
                ->first();

            if ($prevItem) {
                return $this->updateMany($model, $id, $sort, $sortType, $prevItem);
            }

            return true;
        }

        // down
        if ('down' == $action) {
            $nextItem = $builder
                ->where($sortType, '<', $sort)
                ->orderBy($sortType, 'desc')
                ->first();

            if ($nextItem) {
                return $this->updateMany($model, $id, $sort, $sortType, $nextItem);
            }

            return true;
        }

        // top
        if ('top' == $action) {
            return $model::query()->where('id', $id)->update([$sortType => time()]);
        }

        // bottom
        if ('bottom' == $action) {
            $bottomItem = $builder->orderBy($sortType, 'asc')->first();

            return $model::query()
                ->where('id', $id)
                ->update([$sortType => ($bottomItem->$sortType - $bottomItem->id)]);
        }

        return false;
    }

    protected function updateMany($model, $id, $sort, $sortType, $item)
    {
        // 开启事务
        \DB::beginTransaction();
        $updateRes = $model::query()->where('id', $item->id)->update([$sortType => $sort]);
        if ($updateRes) {
            $model::query()->where('id', $id)->update([$sortType => $item->$sortType]);
            \DB::commit();

            return true;
        } else {
            \DB::rollBack();

            return false;
        }
    }
}
