<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Action extends BaseModel
{
    use SoftDeletes;

    protected $table = 'actions';

    /**
     * @var string
     */
    protected $titleColumn = 'name';

    /**
     * @var string
     */
    protected $orderColumn = 'sort';

    /*
     * @var string
     */
    protected $orderWay = 'desc';

    /**
     * @var string
     */
    protected $parentColumn = 'parentId';

    /**
     * @var \Closure
     */
    protected $queryCallback;

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree()
    {
        return $this->buildNestedArray();
    }

    /**
     * Build Nested array.
     *
     * @param array $nodes
     * @param int   $parentId
     *
     * @return array
     */
    protected function buildNestedArray(array $nodes = [], $parentId = 0)
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $node) {
            if ($node[$this->parentColumn] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->getKeyName()]);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Get all elements.
     *
     * @return mixed
     */
    public function allNodes()
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);
        $byOrder = $orderColumn.' = 0,'.$orderColumn.' '.$this->orderWay;

        $self = new static();

        if ($this->queryCallback instanceof \Closure) {
            $self = call_user_func($this->queryCallback, $self);
        }

        return $self->orderByRaw($byOrder)->get()->toArray();
    }

    /**
     * Get options for Select field in form.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function selectOptions()
    {
        $options = (new static())->buildSelectOptions();

        return collect($options)->prepend('Root', 0)->all();
    }

    /**
     * Build options of select field in form.
     *
     * @param array  $nodes
     * @param int    $parentId
     * @param string $prefix
     *
     * @return array
     */
    protected function buildSelectOptions(array $nodes = [], $parentId = 0, $prefix = '')
    {
        $prefix = $prefix ?: str_repeat('&nbsp;', 6);

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $node) {
            $node[$this->titleColumn] = $prefix.'&nbsp;'.$node[$this->titleColumn];
            if ($node[$this->parentColumn] == $parentId) {
                $children = $this->buildSelectOptions($nodes, $node[$this->getKeyName()], $prefix.$prefix);

                $options[$node[$this->getKeyName()]] = $node[$this->titleColumn];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }
}
