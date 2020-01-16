<?php

namespace Common\Logic;

use Common\Model\ArticleModel;

/**
 * 文章逻辑处理层
 * @author 薛松
 */
class ArticleLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		
		$this->modelObj = new ArticleModel();
	}
	
	public function getResult()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() :string
	{
		return ArticleModel::class;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
	 */
	public function hideenComment() :array
	{
		return [
            ArticleModel::$name_d,
		];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
	 */
	public function likeSerachArray() :array
	{
		return [
            ArticleModel::$name_d,
		];
	}
	/**
	 * @name 查看文章分类列表
	 * 
	 * @des 查看文章分类列表
	 * @updated 2018-01-05 12:18
	 */
	public function categoryLists()
	{
        $article_category = M('article_category')
            ->field('name,id')
            ->order('sort DESC')
            ->where('status = 1')
            ->select();
        foreach ($article_category as $k => $v) {
            $r = $this->modelObj
                ->where(['article_category_id' => $v['id'], 'status'=>'1'])
                ->order('sort DESC')
                ->field("name,id,FROM_UNIXTIME(create_time,'%Y-%m-%d %h:%i') create_time")
                ->select();
            $category[ $k ]['name'] = trim($v['name']);
            $category[ $k ]['value'] = $r;
        }
        return $category;
	}
    /**
     * @name 文章列表
     * 
     * @des 文章列表
     * @updated 2018-01-05 12:18
     */
    public function lists()
    {
        //#TODO 这里是查询条件
        $this->searchTemporary = [
            ArticleModel::$name_d => array('like', '%' . $this->data['keyword'] . '%'),
        ];

        //#TODO 这里是要查询的字段如果不传的话默认为表中的所有字段
        $this->searchField = "id, name, intro, FROM_UNIXTIME(create_time,'%Y-%m-%d %h:%i') create_time";

        //#TODO 这里是按照什么排序查询，如果不传默认为ID DESC排序
        $this->searchOrder = 'sort DESC';

        //#TODO 调用通用的获取列表的接口并返回数据  data=>['countTotal'=>2, 'records'=>[.....]]
        $retData = parent::getDataList();
        if(empty($retData) || !isset($retData)){
            $this->errorMessage = '查询数据为空!';
            return [];
        }
        return $retData;
    }
    /**
     * @name 文章详情
     * 
     * @des 文章详情
     * @updated 2018-01-05 12:18
     */
    public function info()
    {
        $retData = $this->modelObj->alias("a")
            ->field("a.article_category_id, a.id, a.name, a.intro, FROM_UNIXTIME(a.create_time,'%Y-%m-%d %h:%i') create_time, b.content")
            ->join('__ARTICLE_CONTENT__ as b on b.article_id = a.id', 'LEFT')
            ->where('id="%s"', $this->data['id'] .', status=1')
            ->find();
        if(empty($retData) || !isset($retData)){
            $this->errorMessage = '查询数据为空!';
            return [];
        }
        //上一篇文章
        $retData['up_article']   =  $this->modelObj
                                    ->where(['article_category_id'=>$retData['article_category_id'], 'id'=>['gt', $retData['id']]])
                                    ->order('sort DESC')
                                    ->field("name,id,FROM_UNIXTIME(create_time,'%Y-%m-%d %h:%i') create_time")
                                    ->find();
        //下一篇文章
        $retData['down_article']   =  $this->modelObj
                                    ->where(['article_category_id'=>$retData['article_category_id'], 'id'=>['lt', $retData['id']]])
                                    ->order('sort DESC')
                                    ->field("name,id,FROM_UNIXTIME(create_time,'%Y-%m-%d %h:%i') create_time")
                                    ->find();
        return $retData;
    }
}
