<?php


namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class Reviews
{
    public function __construct(EntityManager $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function recent($start = 0, $limit = 30)
    {
        /* @var $dbh \Doctrine\DBAL\Driver\PDOConnection */
        $dbh = $this->doctrine->getConnection();
    
        $rows = $dbh->executeQuery(
                "SELECT SQL_CALC_FOUND_ROWS
                r.id,
                r.datecreation,
                r.text,
                r.rawtext,
                r.nbVotes,
                c.id card_id,
                c.name card_name,
                c.code card_code,
                p.name pack_name,
                u.id user_id,
                u.username,
                u.faction usercolor,
                u.reputation,
                u.donation
                from review r
                join user u on r.user_id=u.id
                join card c on r.card_id=c.id
                join pack p on c.pack_id=p.id
                where r.datecreation > DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)
        		and p.dateRelease is not null
                order by r.datecreation desc
                limit $start, $limit")->fetchAll(\PDO::FETCH_ASSOC);
    
        $count = $dbh->executeQuery("SELECT FOUND_ROWS()")->fetch(\PDO::FETCH_NUM)[0];
    
        return array(
                "count" => $count,
                "reviews" => $rows
        );
    
    }

    public function by_author($user_id, $start = 0, $limit = 30)
    {
        /* @var $dbh \Doctrine\DBAL\Driver\PDOConnection */
        $dbh = $this->doctrine->getConnection();
    
        $rows = $dbh->executeQuery(
                "SELECT SQL_CALC_FOUND_ROWS
                r.id,
                r.datecreation,
                r.text,
                r.rawtext,
                r.nbVotes,
                c.id card_id,
                c.name card_name,
                c.code card_code,
                p.name pack_name,
                u.id user_id,
                u.username,
                u.faction usercolor,
                u.reputation,
                u.donation
                from review r
                join user u on r.user_id=u.id
                join card c on r.card_id=c.id
                join pack p on c.pack_id=p.id
                where r.user_id=?
        		and p.dateRelease is not null
        		order by c.code asc
                limit $start, $limit", array(
                        $user_id
                ))->fetchAll(\PDO::FETCH_ASSOC);
    
        $count = $dbh->executeQuery("SELECT FOUND_ROWS()")->fetch(\PDO::FETCH_NUM)[0];
    
        return array(
                "count" => $count,
                "reviews" => $rows
        );
    
    }
}
