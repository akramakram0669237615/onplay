<?php
$type=$_GET['type']??'channels';$allowed=['banners','categories','channels','matches','movies','series','notifications'];if(!in_array($type,$allowed,true))exit('Invalid');$id=isset($_GET['id'])?(int)$_GET['id']:null;$item=$id?find_item($type,$id):[];$page=($id?'تعديل ':'إضافة ').$type;require __DIR__.'/includes/header.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $item=['title'=>trim($_POST['title']??''),'name'=>trim($_POST['name']??''),'description'=>trim($_POST['description']??''),'image'=>trim($_POST['image']??''),'poster'=>trim($_POST['poster']??''),'logo'=>trim($_POST['logo']??''),'url'=>trim($_POST['url']??''),'position'=>(int)($_POST['position']??0),'enabled'=>isset($_POST['enabled'])];
 if($type==='channels')$item['stream']=['type'=>$_POST['stream_type']??'hls','url'=>trim($_POST['stream_url']??'')];
 if($type==='matches'){$item['team_home']=trim($_POST['team_home']??'');$item['team_away']=trim($_POST['team_away']??'');$item['streams']=[['id'=>1,'name'=>'الرئيسي','type'=>$_POST['stream_type']??'hls','url'=>trim($_POST['stream_url']??'')]];}
 if($type==='notifications'){$item['message']=trim($_POST['message']??'');$item['type']=$_POST['notification_type']??'info';}
 if($type==='banners'){$item['action']=['type'=>$_POST['action_type']??'none','target_id'=>(int)($_POST['target_id']??0)];}
 save_item($type,$item,$id);header('Location:content.php?type='.$type);exit;
}
?><div class="titlebar"><h1><?=$id?'تعديل':'إضافة'?> <?=e($type)?></h1><a href="content.php?type=<?=$type?>">← رجوع</a></div><form method="post" class="panel form"><div class="fields">
<label>العنوان<input name="title" value="<?=e($item['title']??'')?>"></label><label>الاسم<input name="name" value="<?=e($item['name']??'')?>"></label>
<label>الوصف<textarea name="description"><?=e($item['description']??'')?></textarea></label><label>رابط الصورة / الشعار<input name="image" value="<?=e($item['image']??$item['logo']??'')?>"></label>
<label>الترتيب<input type="number" name="position" value="<?=e($item['position']??0)?>"></label>
<?php if(in_array($type,['channels','matches'],true)):?><label>نوع البث<select name="stream_type"><option value="hls">HLS</option><option value="dash">DASH</option></select></label><label>رابط البث<input name="stream_url" value="<?=e($item['stream']['url']??$item['streams'][0]['url']??'')?>"></label><?php endif?>
<?php if($type==='matches'):?><label>الفريق الأول<input name="team_home" value="<?=e($item['team_home']??'')?>"></label><label>الفريق الثاني<input name="team_away" value="<?=e($item['team_away']??'')?>"></label><?php endif?>
<?php if($type==='notifications'):?><label>الرسالة<textarea name="message"><?=e($item['message']??'')?></textarea></label><label>النوع<select name="notification_type"><option>info</option><option>update</option><option>warning</option></select></label><?php endif?>
<?php if($type==='banners'):?><label>عند الضغط<select name="action_type"><option value="none">لا شيء</option><option value="open_match">فتح مباراة</option><option value="open_channel">فتح قناة</option><option value="open_url">فتح رابط</option></select></label><label>رقم الهدف<input type="number" name="target_id" value="<?=e($item['action']['target_id']??0)?>"></label><?php endif?>
</div><label class="check"><input type="checkbox" name="enabled" <?=($item['enabled']??true)?'checked':''?>> ظاهر ومفعّل</label><button class="btn">💾 حفظ</button></form><?php require __DIR__.'/includes/footer.php';?>