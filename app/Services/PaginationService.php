<?php
namespace App\Services;


class PaginationService
{
    public function validSort($sort, $sortsValid){
        if(!$sort) return true;
        
        if($sort[0] == '-') return in_array(substr($sort, 1), $sortsValid);

        return in_array($sort, $sortsValid);
        
    }
    public function validFilter($filter, $filtersValid = []){
        if(!$filter) return true;

        $filtersValid[] = 'search';
        foreach ($filter as $key => $value) {
             if(!in_array($key, $filtersValid)) return false;
        }

        return true;
    }

    public static function querySort($sort){
        if(!$sort) return "";

        if($sort[0] == '-'){
           return "ORDER BY ".substr($sort, 1)." DESC";
        }

        return "ORDER BY ".$sort." ASC";
    }

    public function queryFilter($filter, $searchItens, $concat = ''){
        $filters = [];
        foreach ($filter as $key => $value) {
            
            if($key == 'search'){
                foreach ($searchItens as $search) {
                    $filters[] = "$search like '%$value%'";
                }
            }else{
                $filters[] = "$key like '%$value%'";
            }
            
        }
        return count($filters)>0?" $concat (".implode(" OR ", $filters).")":'';
    }

    public static function transformMeta($page, $perPage, $totalFriend){
        return  [
                    "page"=> [
                        "current-page"=> (int)$page,
                        "per-page"=> (int)$perPage,
                        "from"=> $page <= ceil($totalFriend/$perPage) ? (($page-1)*$perPage)+1 : null,
                        "to"=> $page*$perPage<$totalFriend ? $page*$perPage : ($page<=ceil($totalFriend/$perPage) ? $totalFriend:null),
                        "total"=> $totalFriend,
                        "last-page"=>  ceil($totalFriend/$perPage)
                    ]
                ];
        
    }

    
}