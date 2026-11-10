<?php
/** Reproducible offline before/after counts on captured main-link fixtures. */
require_once __DIR__ . '/runtime.php';
$root=dirname(__DIR__,2).'/data/internal-linking-20261110/';
$manifest=json_decode(file_get_contents($root.'manifest.json'),true,512,JSON_THROW_ON_ERROR);
$fixtures=json_decode(file_get_contents($root.'rendered-link-fixtures.json'),true,512,JSON_THROW_ON_ERROR);
$sources=array_column($manifest['sources'],null,'id');$policies=array();
foreach($manifest['anchor_policy'] as $policy){$policies[tzil_url_key($policy['target'])]=$policy;}
function counted_anchors($html,$target){
    $dom=new DOMDocument();$old=libxml_use_internal_errors(true);$dom->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NONET);libxml_clear_errors();libxml_use_internal_errors($old);$xpath=new DOMXPath($dom);$out=array();
    foreach($xpath->query('//main//a') as $a){$href=$a->getAttribute('href');$match=str_contains($target,'bluethesis.ir')?$href===$target:tzil_anchor_key($href)===tzil_url_key($target);if($match){$out[]=array('anchor'=>trim($a->textContent),'href'=>$href,'primary'=>$a->getAttribute('data-tzil-primary'));}}
    return $out;
}
function anchor_counts($html,$policy){$out=array('total'=>0,'exact'=>0,'lsi'=>0,'other'=>0);foreach(counted_anchors($html,$policy['target']) as $a){++$out['total'];if($a['anchor']===$policy['keyword']){++$out['exact'];}elseif(in_array($a['anchor'],$policy['lsi_phrases'],true)){++$out['lsi'];}else{++$out['other'];}}return $out;}
$profiles=array();foreach($policies as $key=>$policy){$profiles[$key]=array('before'=>array('total'=>0,'exact'=>0,'lsi'=>0,'other'=>0),'draft'=>array('total'=>0,'exact'=>0,'lsi'=>0,'other'=>0),'published'=>array('total'=>0,'exact'=>0,'lsi'=>0,'other'=>0));}
$rows=array();
foreach($fixtures as $fixture){
    $source=$sources[$fixture['id']];$state=array('active'=>true,'master_ready'=>false,'remove_bluethesis'=>false,'blue_links'=>$manifest['blue_links'],'anchor_policy'=>$manifest['anchor_policy']);
    $draft=tzil_transform($fixture['html'],$source,$state);if(tzil_transform($draft,$source,$state)!==$draft){throw new RuntimeException('Non-idempotent draft transform');}
    $state['master_ready']=true;$published=tzil_transform($fixture['html'],$source,$state);if(tzil_transform($published,$source,$state)!==$published){throw new RuntimeException('Non-idempotent published transform');}
    $state['remove_bluethesis']=true;$removed=tzil_transform($fixture['html'],$source,$state);
    foreach($policies as $key=>$policy){foreach(array('before'=>$fixture['html'],'draft'=>$draft,'published'=>$published) as $phase=>$html){$counts=anchor_counts($html,$policy);foreach($counts as $name=>$count){$profiles[$key][$phase][$name]+=$count;}if($phase!=='before'&&$counts['other']){throw new RuntimeException('Unapproved rendered money anchor');}$active=$phase==='published'||$policy['activation']==='immediate';if($phase!=='before'&&$counts['exact']!==($active&&in_array($fixture['id'],$policy['designated_exact_source_ids'],true)?1:0)){throw new RuntimeException('Exact source designation failed: '.$source['url']);}}}
    foreach($source['links'] as $link){
        $key=tzil_url_key($link['target']);$policy=$policies[$key]??array('keyword'=>$link['anchor'],'target'=>$link['target'],'lsi_phrases'=>array());
        $before=anchor_counts($fixture['html'],$policy);$after=anchor_counts($draft,$policy);$future=anchor_counts($published,$policy);$active=$link['activation']!=='master-published';
        foreach(array($active?$draft:null,$published) as $html){if($html===null){continue;}$all=counted_anchors($html,$link['target']);if(isset($policies[$key])){$primary=array_filter($all,static fn($a)=>$a['primary']===$key&&$a['anchor']===$link['anchor']);if(count($primary)!==1){throw new RuntimeException('Missing approved primary: '.$source['url']);}}elseif(count($all)!==1||$all[0]['anchor']!==$link['anchor']){throw new RuntimeException('Verified-map exact single-link contract failed');}}
        $rows[]=array($source['url'],$policy['keyword'],$link['anchor'],$link['anchor_role']??'verified-exact',$link['target'],$before['total'],$before['exact'],$before['lsi'],$after['total'],$after['exact'],$after['lsi'],$future['total'],$future['exact'],$future['lsi'],count(counted_anchors($removed,$link['target'])));
    }
    foreach($manifest['blue_links'] as $blue){if($blue['source_id']!==$fixture['id']){continue;}$count=static fn($html)=>count(array_filter(counted_anchors($html,$blue['target']),static fn($a)=>$a['anchor']===$blue['anchor']));if($count($fixture['html'])!==1||$count($draft)!==1||$count($removed)!==0){throw new RuntimeException('Blue removal flag contract failed');}$rows[]=array($source['url'],'',$blue['anchor'],'audit',$blue['target'],1,0,0,1,0,0,1,0,0,0);}
}
$out=fopen('php://output','w');
if(in_array('--profiles',$argv,true)){
    fputcsv($out,array('keyword','target','sample_source_count','before_total','before_exact','before_lsi','before_other','draft_total','draft_exact','draft_lsi','after_master_total','after_master_exact','after_master_lsi','draft_lsi_per_exact','after_master_lsi_per_exact','after_master_within_3_to_4'),',','"','');
    foreach($policies as $key=>$policy){$p=$profiles[$key];$draft_ratio=$p['draft']['exact']?$p['draft']['lsi']/$p['draft']['exact']:null;$future_ratio=$p['published']['exact']?$p['published']['lsi']/$p['published']['exact']:null;fputcsv($out,array($policy['keyword'],$policy['target'],count($fixtures),$p['before']['total'],$p['before']['exact'],$p['before']['lsi'],$p['before']['other'],$p['draft']['total'],$p['draft']['exact'],$p['draft']['lsi'],$p['published']['total'],$p['published']['exact'],$p['published']['lsi'],$draft_ratio,$future_ratio,$future_ratio!==null&&$future_ratio>=3&&$future_ratio<=4?'yes':'no'),',','"','');}
}else{
    fputcsv($out,array('source_url','keyword','primary_anchor','anchor_role','target','before_target_links','before_exact_links','before_lsi_links','after_draft_target_links','after_draft_exact_links','after_draft_lsi_links','after_master_target_links','after_master_exact_links','after_master_lsi_links','after_optional_blue_removal_target_links'),',','"','');foreach($rows as $row){fputcsv($out,$row,',','"','');}
}
