<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    private $pwd;

    public function __construct()
    {
        $this->pwd = $this->config('pwd');
        $this->validatePWD();
    }

    protected function config($key)
    {
        return db('config')->where('key',$key)->value('value');
    }

    protected function validatePWD()
    {
        //echo $this->pwd;
        if (input('pwd') != $this->pwd || !input('?pwd'))
        {
            exit('pwd error');
        }
    }

    public function index()
    {
        return 'welcome';
    }

    protected function add()
    {
        if (!input('?post.info'))return 'param fail';
        $input = json_decode(input('post.info'),true);
        //var_dump($input);
        $r = db('sms')->insert($input);
        if ($r == 1)
        {
            return 'success';
        }
        else
        {
            return 'insert fail';
        }
    }

    protected function adds()
    {
        $input = json_decode(input('post.info'),true);

        $r = db('sms')->insertAll($input);
        if ($r !== false)
        {
            return 'success';
        }
        else
        {
            return 'insert fail';
        }
    }

    public function fetchLastOne()
    {
        $info  = db('sms')->order('datetime','desc')->limit(1)->find();
        return $this->showOne($info['id'],$info['datetime'],$info['phone'],$info['content']);
    }

    private function showOne($id,$datetime,$phone,$content)
    {
        $echoo =    '短信索引：'.$id.'<br>'.
                    '时间：'.$datetime.'<br>'.
                    '电话号码：'.$phone.'<br>'.
                    '内容：'.$content.'<hr>';
        return $echoo;
    }

    public function fetchAll()
    {
        $listRows = 10;
        $total = ceil(db('sms')->count()/$listRows);
        $page = input('?page')?input('page'):1;
        $r = db('sms')->page($page,$listRows)->order('id','desc')->select();
        $out = null;
        foreach ($r as $info) {
            $out .= $this->showOne($info['id'],$info['datetime'],$info['phone'],$info['content']);
        }
        $prev = $page==1?'':'<a href="'.url('index/index/fetchAll',['page' => $page-1,'pwd' => $this->pwd]).'">上一页</a>&nbsp;&nbsp;';
        $next = $page<$total?'<a href="'.url('index/index/fetchAll',['page' => $page+1,'pwd' => $this->pwd]).'">下一页</a>':'';
        return $out.'当前位置：第'.$page.'页/共'.$total.'页<br>'.$prev.$next;
    }

    /**
     * 同步短信
     */
    public function sync()
    {
//        exit(input('post.info'));
        if (!input('?post.info'))return 'param error';
        $input = json_decode(input('post.info'),true);
        //var_dump($input);
        global $first_data_key;
        $first_data_key = null;
        if (db('sms')->count() == 0)
        {
            $first_data_key = null;
        }
        else
        {
            foreach ($input as $key => $item) {
                $begin = db('sms')->where([
                    'datetime' => $item['datetime'],
                    'content' => $item['content'],
                    'phone' => $item['phone']
                ])->find();
                if (!empty($begin) && $begin != null)
                {
                    //无需同步
                    if ($key == 0)return 'success';
                    $first_data_key = $key;
                    break;
                }
            }
        }
        $adds = array_slice($input,0,$first_data_key);
        if (!empty($adds))
        {
            $r = db('sms')->insertAll($adds);
        }
        else
        {
            $r = true;
        }
        if ($r !== false)
        {
            return 'success';
        }
        else
        {
            return 'insert fail';
        }
    }

}
