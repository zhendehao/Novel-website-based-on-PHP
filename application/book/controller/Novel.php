<?php


namespace app\book\controller;
use app\book\controller\Base;
use think\Db;

class Novel extends Base
{
    protected $title = array();
    protected $book = array();
    protected $key = 0;
    protected $url = "https://www.jx.la";

    public function novel()
    {

        return $this->fetch();
    }

    public function index()
    {
        $title = "novel-index";
        $this->assign('title',$title);

        return $this->fetch();
    }

    public function info()
    {
        $title = "novel-info";
        $this->assign('title',$title);
        return $this->fetch();
    }

    public function read()
    {
        $title = "novel-read";
        $this->assign('title',$title);
        return $this->fetch();
    }
    public function read1(){
        return $this->fetch();
    }

    public function tags()
    {
        $title = "novel-tags";
        $this->assign('title',$title);
        return $this->fetch();
    }

    public function spider()
    {

//        $con = mysqli_connect("localhost","root","123456");
//        if (!$con)
//        {
//            die('Could not connect: ' . mysqli_error());
//        }
//
//        mysqli_select_db($con,"book");


//$url="http://www.biquge.la";
        //$url="https://www.jx.la";
        $this->gettitle($this->url);
        //$con->close();
    }

    protected function curl_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    protected function gettitle($value)
    {
        $html = $this->curl_get_contents($value);
        //获取书名/book/123457/
        preg_match_all("/\/book\/[0-9]{1,7}\//i", $html, $match1);


        foreach ($match1[0] as $key1 => $value1) {
            //判断书名是否在已经存在
            $ssa = array_search($value1, $this->book);

//            if ($ssa === false) {
//                //var_dump($value1);
//                $this->book[] = $value1;
//                //地址https://www.jx.la/book/123457/
//                $url_book = $this->url . $value1;
//                //记录地址
//                file_put_contents(__PUBLIC__ . "book.txt", $url_book . PHP_EOL, FILE_APPEND);
//                //通过地址获取小说的章节内容
//                $html_book = $this->curl_get_contents($url_book);
//
//                $url_book_array = explode("/", $url_book);
//
//                $count_book_num = count($url_book_array);
//
//                $book_num = $url_book_array[$count_book_num - 2];
//
//                //$html_book=mb_convert_encoding($html_book, "UTF-8", "GBK");
//
//                preg_match_all("/<dd>.*<\/dd>/i", $html_book, $match_book);
//                preg_match_all("/<title>.*<\/title>/i", $html_book, $match_book_title_array);
//
//                $match_book_title = preg_replace("/<title>/", "", $match_book_title_array[0][0]);
//                $match_book_title = preg_replace("/<\/title>/", "", $match_book_title);
//                $match_book_title_arrayone = explode("_", $match_book_title);
                //var_dump($match_book_title_arrayone[0]);
                //mysqli_query("INSERT INTO `id` (`id` ,`name` ,`txt`) VALUES (NULL , '".$match_book_title_arrayone[0]."', '".$book_num.".txt"."')");
                //$result = Db::execute("insert into `yg_id` (`id` ,`name` ,`txt`) values (NULL , '".$match_book_title_arrayone[0]."', '".$book_num.".txt"."')");
                //首页的小说列表$match_book[0]
//                foreach ($match_book[0] as $key_book_list => $value_book_list) {
//
//                    $chapter_array=explode("\"", $value_book_list);
//
//                    foreach ($chapter_array as $key_chapter => $value_chapter) {
//                        if (preg_match("/[0-9]{1,9}\.html/", $value_chapter)) {
//
//                            $html_chapter=$this->curl_get_contents($url_book.$value_chapter);
//                            //$html_chapter=mb_convert_encoding($html_chapter, "UTF-8", "GBK");
//
//                            preg_match_all("/<div id=\"content\">.*?<\/div>/is", $html_chapter, $match_chapter);
//                            preg_match_all("/<title>.*<\/title>/i", $html_chapter, $match_title);
//                            //preg_match_all("/<div id=\"contetn\">.*<\/div>/i", $html_chapter, $match_content);
//
//                            $value_content= $match_title[0][0].PHP_EOL.$match_chapter[0][0];
//
//
//                            $value_content=str_replace("<br>", PHP_EOL, $value_content);
//
//                            $value_content=str_replace("&nbsp;", " ", $value_content);
//
//                            $value_content=preg_replace("/<script>.*<\/script>/", "", $value_content);
//
//                            $value_content=preg_replace("/<title>/", "", $value_content);
//                            $value_content=preg_replace("/<\/title>/", "", $value_content);
                /*                            $value_content=preg_replace("/<.*?>/", "", $value_content);*/
//
//
//                            file_put_contents(__PUBLIC__."book\\".$book_num.".txt",$value_content.PHP_EOL,FILE_APPEND);
//
//                        }
//                    }
//                }
//            }
//            }
            var_dump($html);

            preg_match_all("/http:\/\/www.jx.la\/[a-z]{8,20}\//i", $html, $match);
            var_dump($match);

            echo $this->key;
            $this->key++;



            //while(list($key,$value) = each($match[0]))
            foreach ($match[0] as $key => $value) {
                $ss = array_search($value, $this->title);
                if ($ss === false) {
                    //var_dump($value);
                    $this->title[] = $value;
                    file_put_contents(__PUBLIC__ . "title.txt", $value . PHP_EOL, FILE_APPEND);
                    die;
                    $this->gettitle($value);
                }
            }
        }
    }


}

