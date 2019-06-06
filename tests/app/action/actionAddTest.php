<?php

namespace Test\App\Role;

use Tests\CommonTestCase;

class actionAddTest extends CommonTestCase
{
    // 添加权限测试
    public function testActionAdd()
    {
        $params = [
            'name' => '测试权限',
            'route' => 'Delete:/admin/articles',
            'parentId' => 0,
            'description' => '这是一条测试数据',
        ];
        $response = $this->json('POST', '/admin/actions', $params, $this->header);
        $result = json_decode($response->response->content(), true);
        $this->assertEquals(0, $result['errorCode']);

        // 查询列表总数
        $response = $this->json('GET', '/admin/actions', [], $this->header);
        $result = json_decode($response->response->content(), true);

        $this->assertEquals(3, count($result['data']));
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
