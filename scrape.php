<?php
include("random_user_agent.php");

function curl($url,$posts=""){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT,random_user_agent());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $icerik = curl_exec($ch);
    return $icerik;
    curl_close($ch);
}

include 'simple_html_dom.php';

/*
In this example we traverse the DOM of http://www.hltv.org/results/
scraping information related to the outcomes of CS matches. Most of 
the work is accomplished by the ->find() methond. This can search for 
many things including specific div classes. Other helpful methods are 
the child(),parent(), and sibling() methods that allow you to traverse
reletave to your current position.
*/

//Start Example

$html = new simple_html_dom();
$html->load(curl("http://www.hltv.org/results/"));

$out = array();
$out_n=0;

$date = null;
//for each match
foreach($html->find('div[class=matchListBox]') as $match) {
    //get match info
    //get date
    $prev = $match->prev_sibling();
    if($prev->class == 'matchListDateBox') {
        $date = $prev->plaintext;
    }
    $out[$out_n]['date'] = $date;    
    //get type/map (bo3,bo1,..)
    $map = $match->find('div[class=matchTimeCell]',0);
    $out[$out_n]['type'] = $map->plaintext;
    //get team 1
    $team1 = $match->find('div[class=matchTeam1Cell]',0);
    $out[$out_n]['team1'] = $team1->plaintext;
    //get team 2
    $team2 = $match->find('div[class=matchTeam2Cell]',0);
    $out[$out_n]['team2'] = trim($team2->plaintext);
    //get team 1 score
    $score = $match->find('div[class=matchScoreCell]',0);
    $team1_score = $score->children(0)->plaintext;
    $out[$out_n]['team1_score'] = $team1_score;
    //get team 2 score    
    $team2_score = $score->children(1)->plaintext;
    $out[$out_n]['team2_score'] = $team2_score;
    //decide winner
    $out[$out_n]['winner'] = $team1->plaintext;
    if ($team2_score > $team1_score) {
        $out[$out_n]['winner'] = trim($team2->plaintext);
    }
    
    $out_n++;
}


echo '<pre>';
print_r($out);
//print_r($html);
echo '</pre>';




?>
