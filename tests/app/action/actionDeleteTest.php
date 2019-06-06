<?php

namespace Test\App\Role;

use Tests\CommonTestCase;

class actionDeleteTest extends CommonTestCase
{
    // 删除单个权限测试
    public function testActionDelete()
    {
        $params = [
            'ids' => [2],
        ];
        $response = $this->json('DELETE', '/admin/actions', $params, $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(0, $result['errorCode']);

        // 查询列表总数
        $response = $this->json('GET', '/admin/actions', [], $this->header);
        $result = json_decode($response->response->content(), true);

        $this->assertEquals(1, count($result['data']));
        $this->initSystemDataSet();
    }

    /**
     * 预加载数据.
     *
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet($this->dataSet('action/action-list.yaml'));
    }
}
