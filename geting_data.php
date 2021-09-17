<?php
    $content = file_get_contents('https://www.leagueofgraphs.com/pl/champions/counters');
    $adap_json = file_get_contents("adap.json");

    $json_content = json_decode($adap_json);

    echo $json_content[1]->name;
    echo $json_content[1]->adap;

    $pattern = '/<span class="name">(.*?)<\/span>/s';
    preg_match_all($pattern, $content, $matches);
    $names = $matches[1];

    $pattern = '/<i>(.*?)<\/i>/s';
    preg_match_all($pattern, $content, $matches);
    $role = $matches[1];

    $pattern = '/data-value="(.*)\"/U';
    preg_match_all($pattern, $content, $matches);
    $winrate = $matches[1];

    $pattern = '/<span class="name hide-for-small-down">(.*?)<\/span>/s';
    preg_match_all($pattern, $content, $matches);
    $scc = $matches[1];

    $pattern = '/<span class="subname">(.*?)<\/span>/s';
    preg_match_all($pattern, $content, $matches);
    $scc_value = $matches[1];

    $synergy = [];
    $counters = [];
    $counter = [];
    $synergy_val = [];
    $counters_val = [];
    $counter_val = [];

    $j = 0;
    for($i = 0; $i < count($scc); $i += 3)
    {
        $synergy[$j] = $scc[$i];
        $counters[$j] = $scc[$i+1];
        $counter[$j] = $scc[$i+2];

        $synergy_val[$j] = $scc_value[$i];
        $counters_val[$j] = $scc_value[$i+1];
        $counter_val[$j] = $scc_value[$i+2];

        $j++;
    }


    for($i = 0; $i < count($names); $i++)
    {
        $resoult[$i] = array(
            "name" => $names[$i],
            "winrate" => $winrate[$i],
            "synergy" => $synergy[$i],
            "synergy_val" => substr($synergy_val[$i], 50, -45),
            "counters" => $counters[$i],
            "counters_val" => substr($counters_val[$i], 50, -45),
            "counter" => $counter[$i],
            "counter_val" => substr($counter_val[$i], 50, -45),
            "role" => role($role[$i]),
            "adap" => adap($json_content, $names[$i])
        );
    }
    header('Content-Type: application/json');
    $data = json_encode($resoult, JSON_PRETTY_PRINT);
    file_put_contents("champions_statistics.json", $data);
    echo $data;

    function role($role)
    {
        $resoult = [];
        $roles = explode(",", $role);
        $r = 0;

        foreach($roles as $i)
        {
            if(strpos($i,"Top"))
            {
                $r = 1;
            }
            if(strpos($i,"Jungler"))
            {
                $r = 2;
            }
            if(strpos($i,"Środkowa linia"))
            {
                $r = 3;
            }
            if(strpos($i,"AD Carry"))
            {
                $r = 4;
            }
            if(strpos($i,"Support"))
            {
                $r = 5;
            }
            array_push($resoult, $r);
        }
        return $resoult;
    }


    function adap($json_content, $name)
    {
        foreach($json_content as $champ)
        {
            if($name == $champ->name)
            {
                return $champ->adap;
            }
        }
    }

?>