<?php
function normalizeSectorKey($value){
 $raw=trim((string)$value); if($raw==='') return '';
 $lower=function_exists('mb_strtolower')?mb_strtolower($raw,'UTF-8'):strtolower($raw);
 $ascii=@iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$lower);
 $ascii=$ascii!==false?strtolower($ascii):$lower;
 $ascii=preg_replace('/[^a-z0-9]+/','',$ascii);
 $map=['assurance'=>'assurance','assurances'=>'assurance','renovation'=>'renovation','renovations'=>'renovation','energie'=>'energie','energies'=>'energie','finance'=>'finance','finances'=>'finance','garage'=>'garage','garages'=>'garage','telecom'=>'telecom','telecoms'=>'telecom','general'=>'general','generale'=>'general','generaliste'=>'general'];
 return $map[$ascii] ?? $ascii;
}
function normalizeSectors($input){
 if(is_array($input)) $values=$input; else {$raw=trim((string)$input); if($raw==='') return []; $decoded=json_decode($raw,true); if(json_last_error()===JSON_ERROR_NONE && is_array($decoded)) $values=$decoded; else $values=preg_split('/[;,|]/',$raw);}
 $n=[]; foreach($values as $v){$val=trim((string)$v); if($val==='') continue; $n[]=normalizeSectorKey($val);} return array_values(array_unique($n));
}
$pdo=new PDO('mysql:host=127.0.0.1;port=3308;dbname=rappel;charset=utf8mb4','root','');
$provider='d1fbcb16-2983-4975-bd45-7401ca7f6e5b';
$sectors=$pdo->query("SELECT sectors FROM user_profiles WHERE id='$provider'")->fetchColumn();
$allowed=normalizeSectors($sectors);
$st=$pdo->prepare("SELECT l.*,la.provider_id as assigned_to FROM leads l LEFT JOIN lead_assignments la ON l.id=la.lead_id WHERE (la.provider_id IS NULL OR la.provider_id = ?) ORDER BY l.created_at DESC");
$st->execute([$provider]);
$rows=$st->fetchAll(PDO::FETCH_ASSOC);
$f=array_values(array_filter($rows,function($r)use($allowed){$s=normalizeSectorKey($r['sector']??''); return $s!=='' && in_array($s,$allowed,true);}));
echo 'allowed='.json_encode($allowed).PHP_EOL;
echo 'rows='.count($rows).' filtered='.count($f).PHP_EOL;
foreach(array_slice($f,0,8) as $r){echo $r['id'].' | '.$r['name'].' | '.$r['sector'].' | '.$r['status'].' | '.($r['assigned_to']?:'NULL').PHP_EOL;}
?>
