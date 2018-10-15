<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 分页类
 * User: yc
 * Date: 14-10-29
 * Time: 下午8:21
 */

class Page {
    private $total              = '0';                          //信息条数
    private $perPage            = '20';                         //默认每页信息条数
    private $url                = 'indexaction/index/';         //基本url
    private $page               = '1';                          //当前页数
    private $maxSize            = '5';                          //页数最大显示数量
    private $isFirst            = '1';                          //是否包含首页尾页
    private $isprev             = '1';                          //是否包含上一页下一页
    private $prevClass          = 'syy';                        //上一页class
    private $nextClass          = 'xyy';                        //下一页class
    private $firstClass         = 'sy';                         //首页class
    private $endClass           = 'my';                         //尾页class
    private $pageHtml           = '';                           //分页信息

    public function __construct(){

    }

    /*返回数据*/
    public function data($arr = array()){
        if(count($arr) > 0){
            $this->initialize($arr);//参数初始化
            $this->doPage();//执行分页函数
        }
        return $this->pageHtml;
    }

    /*初始化类参数*/
    private function initialize($arr){
        foreach($arr as $key => $value){
            if(!empty($value)){
                $this->$key         = $value;
            }
        }
    }

    /*执行分页*/
    private function doPage(){
        $total              = $this->total;
        $perPage            = $this->perPage;
        $url                = $this->url;
        $prevClass          = $this->prevClass;
        $nextClass          = $this->nextClass;
        $firstClass         = $this->firstClass;
        $endClass           = $this->endClass;
        $maxSize            = $this->maxSize;
        $page               = $this->page;

        $pageHtml           = '';

        $pages              = ceil($total / $perPage);//总页数

        /*获得页数第最大值和最小值*/
        if($pages <= $maxSize){//总页数小于页面最大显示数量
            $minPage        = 1;
            $maxPage        = $pages;
        }else{
            if($page < ceil($maxSize / 2)){
                $minPage        = 1;
                $maxPage        = $maxSize;
            }else{
                $minPage        = $page - ($maxSize - ceil($maxSize / 2));
                $maxPage        = $minPage + $maxSize -1;
            }
        }
        $maxPage            = ($maxPage > $pages)?$pages:$maxPage;
        $minPage            = ($minPage < 1)?1:$minPage;

        /*加载总共页数、条数 */
        if($this->isFirst > 0){
            $pageHtml           .= '<span>共'.$total.'条</span>&nbsp;&nbsp;';
            $pageHtml           .= '<span>共'.$pages.'页</span>&nbsp;&nbsp;';
        }

        /*加载首页*/
        if($this->isFirst > 0){
            $pageHtml           .= '<a class="'.$firstClass.'" href="'.$url.'1">首页</a>';
        }

        /*加载上一页*/
        if($this->isprev > 0){
            $pageHtml           .= '<a class="'.$prevClass.'" href="'.$url.((($page-1) < 1)?1:($page-1)).'">上一页</a>';
        }

        /*加载数字页码*/
        for($i = $minPage; $i <= $maxPage; $i++){
            $isOn               = ($page == $i)?'on':'';
            $pageHtml           .= '<a class="'.$isOn.'" href="'.$url.$i.'">'.$i.'</a>';
        }

        /*加载下一页*/
        if($this->isprev > 0){
            $pageHtml           .= '<a class="'.$nextClass.'" href="'.$url.((($page+1) > $pages)?$pages:($page+1)).'">下一页</a>';
        }

        /*加载尾页 */
        if($this->isFirst > 0){
            $pageHtml           .= '<a class="'.$endClass.'" href="'.$url.$pages.'">尾页</a>';
        }

        $this->pageHtml     = $pageHtml;
    }
}