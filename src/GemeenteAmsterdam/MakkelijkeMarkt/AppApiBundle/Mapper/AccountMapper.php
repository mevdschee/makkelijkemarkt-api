<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper;

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Account;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\AccountModel;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 */
class AccountMapper
{
    /**
     * @param Account $e
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\AccountModel
     */
    public function singleEntityToModel(Account $e)
    {
        $object = new AccountModel();
        $object->id = $e->getId();
        $object->naam = $e->getNaam();
        $object->email = $e->getEmail();
        $object->username = $e->getUsername();
        $object->roles = $e->getRoles();
        $object->locked = $e->getLocked();
        return $object;
    }

    /**
     * @param multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Account[] $list
     * @return multitype:\GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Model\AccountModel[]
     */
    public function multipleEntityToModel($list)
    {
        $result = [];
        foreach ($list as $e)
        {
            /* @var $e Account */
            $result[] = $this->singleEntityToModel($e);
        }
        return $result;
    }
}