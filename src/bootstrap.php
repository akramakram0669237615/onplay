<?php
declare(strict_types=1);
$config=require __DIR__.'/../config.php';
session_name($config['session_name']); session_start();

function data_path(string $name):string{global $config;return $config['data_dir'].'/'.preg_replace('/[^a-z0-9_-]/i','',$name).'.json';}
function db_read(string $name):array{$p=data_path($name);if(!is_file($p))return[];$x=json_decode((string)file_get_contents($p),true);return is_array($x)?$x:[];}
function db_write(string $name,array $data):bool{$p=data_path($name);$tmp=$p.'.tmp';$j=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);if($j===false)return false;if(file_put_contents($tmp,$j,LOCK_EX)===false)return false;return rename($tmp,$p);}
function next_id(array $a):int{$m=0;foreach($a as $x)$m=max($m,(int)($x['id']??0));return $m+1;}
function find_item(string $file,int $id):?array{foreach(db_read($file) as $x)if((int)($x['id']??0)===$id)return$x;return null;}
function save_item(string $file,array $item,?int $id=null):int{$all=db_read($file);if($id){foreach($all as $k=>$x)if((int)($x['id']??0)===$id){$item['id']=$id;$all[$k]=$item;db_write($file,$all);return$id;}}$item['id']=next_id($all);$all[]=$item;db_write($file,$all);return$item['id'];}
function delete_item(string $file,int $id):void{db_write($file,array_values(array_filter(db_read($file),fn($x)=>(int)($x['id']??0)!==$id)));}
function is_admin():bool{return isset($_SESSION['user']);}
function current_user():?array{if(!isset($_SESSION['user']))return null;return find_item('users',(int)$_SESSION['user']);}
function has_role(string $role):bool{$u=current_user();return $u && (($u['role']??'')==='owner'||($u['role']??'')===$role);}
function require_login():void{if(!is_admin()){header('Location:login.php');exit;}}
function require_owner():void{require_login();if(!has_role('owner')){http_response_code(403);exit('غير مصرح لك بهذه العملية');}}
function e($s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function api_out(array $x,int $code=200):never{http_response_code($code);header('Content-Type: application/json; charset=utf-8');header('Access-Control-Allow-Origin: *');echo json_encode($x,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);exit;}
