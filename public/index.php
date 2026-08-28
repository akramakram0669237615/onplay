<?php
require __DIR__.'/../src/bootstrap.php';
$path=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/';
$pos=strpos($path,'/api/');if($pos===false)api_out(['success'=>false,'error'=>['code'=>404,'message'=>'API endpoint not found.']],404);
$r=explode('/',trim(substr($path,$pos+5),'/'));if(array_shift($r)!=='v1')api_out(['success'=>false],404);
$visible=fn($x)=>array_values(array_filter($x,fn($a)=>($a['enabled']??true)!==false));
$type=$r[0]??'';
if($type==='home'){ $h=db_read('home');api_out(['success'=>true,'page'=>'home','data'=>$h]);}
if($type==='app'&&($r[1]??'')==='config')api_out(['success'=>true,'app'=>db_read('settings')]);
if($type==='notifications')api_out(['success'=>true,'items'=>$visible(db_read('notifications'))]);
$files=['categories','channels','matches','movies','series'];
if(in_array($type,$files,true)&&count($r)===1)api_out(['success'=>true,'page'=>$type,'items'=>$visible(db_read($type))]);
if(in_array($type,$files,true)&&isset($r[1])){
 $x=find_item($type,(int)$r[1]);if(!$x)api_out(['success'=>false,'error'=>['code'=>404,'message'=>'Not found']],404);
 if(($r[2]??'')==='episodes')api_out(['success'=>true,'episodes'=>$x['episodes']??[]]);
 api_out(['success'=>true,'data'=>$x]);
}
if($type==='search'){$q=mb_strtolower((string)($_GET['q']??''));$out=[];foreach($files as $f)foreach($visible(db_read($f)) as $x){$s=mb_strtolower(($x['name']??'').' '.($x['title']??''));if($q!==''&&mb_strpos($s,$q)!==false)$out[]=['type'=>$f,'id'=>$x['id'],'title'=>$x['name']??$x['title']??''];}api_out(['success'=>true,'items'=>$out]);}
api_out(['success'=>false,'error'=>['code'=>404,'message'=>'API endpoint not found.']],404);
