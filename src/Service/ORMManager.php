<?php

/**
 * This service provides basic ways to access data on DB by ORM features.
 * It doesn't have intelligence or knowledge of environment where it's used.
 * It only know that could work with tables having off and sent flags.
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Utenti;
use App\Entity\Entexevento;
use App\Entity\Rxsezioni;
use App\Entity\Rxvotanti;

use App\Service\RTSentableInterface;


/**
 * It provides standard ways to access ORM objects.
 * It let simple to work with Query Builder or Repository Objects, hiddening to the client the complexity.
 * For Divo entities having off flag, it is able to return only active records.
 * Off flag can have boolean values: true= record disabled, false or null=record enabled
 * Sent flag can have Bit values: 0=not sent yet, 1=sent with success
 */
class ORMManager {
    // variables
    private $manager;
    private $payload;
    private $entities;
    private $serviceUser;

    /**
     * Build an ORMmanager instance
     */
    public function __construct(Security $user, EntityManagerInterface $manager) 
    {
        $this->entities = [];
        $this->serviceUser = null;
        $this->manager = $manager;
        $repository = $manager->getRepository(Utenti::class);
        // look for a single Product by name
        $this->serviceUser = $repository->findOneBy(['user_id' => $user->getUser()->getId()]);
        // The expensive process (e.g.,db connection) goes here.
    }

    /**
     * Return the service user of logged user using the system
     */
    function getServiceUser() 
    {
        return $this->serviceUser;
    }

    /** 
     * Return manager used by ORMmanager.
     */
    function getManager() 
    {
        return $this->manager;
    }

    /**
     * Set manager into ORMmanager.
     * This manager will be used in order to perform queries on database
     */
    private function setManager($manager) 
    {
        $this->manager = $manager;
        $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    /**
     * It begins a transaction with the database.
     * Until that you don't invoke commit() or rollback() methods, the changes it will be not applied.
     */
    function beginTransaction() 
    {
        $this->manager->getConnection()->beginTransaction();
    }

    /**
     * Commit a transaction started with beginTransaction() method
     */
    function commit() 
    {
        $this->manager->flush();
        $this->manager->getConnection()->commit();
        $this->manager->clear();
    }

    /**
     * Rollback a transaction started with beginTransaction() method
     */
    function rollback() 
    {
        $this->manager->getConnection()->rollback();
        $this->manager->clear();
    }

    /**
     * It persists and flush the given entity into database.
     * This is a basic method in order to save entity status on database.
     */
    function insertEntity($entity) 
    {
        $this->manager->persist($entity);
        // actually executes the queries (i.e. the INSERT query)
        $this->manager->flush();
    }

    /**
     * Update all changed entities
     */
    function updateEntity()
    {
        $this->manager->flush();
    }

    /**
     * Set as Sent=1 the given entity
     */
    function setSent($entity) 
    {
        $entity->setSent(1);
        $this->manager->flush();
    }

    /**
     * Set as Sent=1 the given array of entities
    */
    function setSentArray(array $entities) 
    {
        foreach ($entities as $entity) {
            $entity->setSent(1);
        }
        $this->manager->flush();
    }

    /**
     * Disable the given entity
     */
    public function setOff($entity) 
    {
        $entity->setOff(true);
        $this->manager->flush();
    }

    /**
     * Disable the given array of entities
     */
    public function setOffArray(array $entities)
    {
        foreach ($entities as $entity) {
            $entity->setOff(true);
        }
        $this->manager->flush();
    }

    /**
     * This is the brain that performs queries on data tables
     */
    private function mineEntities(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null, string $sentParameter = null)
    {
        $index = 1;
        $qb = $this->manager->createQueryBuilder();
        $qb->select('e')
                ->from($entityInterface->getName(), 'e')
                ->where('1=1');
        if ($entityInterface->isOffable()) {
            $qb->andWhere('e.off != ?'.$index.' OR e.off is null')->setParameter(''.$index, 'true');
            $index++;
        }
        if ($entityInterface->isSentable()) {
            $qb->andWhere('e.sent = ?'.$index)->setParameter(''.$index, $sentParameter);
            $index++;
        }
        if (isset($parameters)) {
            foreach ($parameters as $key => $param) {
                $qb->andWhere('e.' . $key . ' = ?' . $index)->setParameter($index, $param);
                $index++;
            }
        }
        if (isset($ordering)) {
            foreach ($ordering as $keyOrd => $order) {
                $qb->orderBy('e.' . $keyOrd , $order);
            }
        }
        $q = $qb->getQuery();

        $objects = $q->execute();
        return $objects;
    }


     /**
     * This is the brain that performs queries on data tables
     */
    private function mineEntitiesIN(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null, string $sentParameter = null)
    {
        $index = 1;
        $qb = $this->manager->createQueryBuilder();
        $qb->select('e')
                ->from($entityInterface->getName(), 'e')
                ->where('1=1');
        if ($entityInterface->isOffable()) {
            $qb->andWhere('e.off != ?'.$index.' OR e.off is null')->setParameter(''.$index, 'true');
            $index++;
        }
        if ($entityInterface->isSentable()) {
            $qb->andWhere('e.sent = ?'.$index)->setParameter(''.$index, $sentParameter);
            $index++;
        }
        if (isset($parameters)) {
            foreach ($parameters as $key => $param) {
                $qb->andWhere('e.' . $key . ' IN (?' . $index.')')->setParameter($index, $param);
                $index++;

            }
        }
        if (isset($ordering)) {
            foreach ($ordering as $keyOrd => $order) {
                $qb->orderBy('e.' . $keyOrd , $order);
            }
        }
        $q = $qb->getQuery();

        $objects = $q->execute();
        return $objects;
    }


    /**
     * This is the brain that performs queries on data tables
     */
    public function getAllEntitiesByParameters(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null)
    {
        $index = 1;
        $qb = $this->manager->createQueryBuilder();
        $qb->select('e')
                ->from($entityInterface->getName(), 'e')
                ->where('1=1');
        
        if (isset($parameters)) {
            foreach ($parameters as $key => $param) {
                $qb->andWhere('e.' . $key . ' = ?' . $index)->setParameter($index, $param);
                $index++;
            }
        }
        if (isset($ordering)) {
            foreach ($ordering as $keyOrd => $order) {
                $qb->orderBy('e.' . $keyOrd , $order);
            }
        }
        $q = $qb->getQuery();

        $objects = $q->execute();
        return $objects;
    }



    /**
     * This is the brain that performs queries on data tables
     */
    public function deleteAllEntitiesByParameters(RTSentableInterface $entityInterface, array $parameters = null)
    {
        $index = 1;
        $qb = $this->manager->createQueryBuilder();
    
        $qb->delete($entityInterface->getName(), 'e')
                ->where('1=1');
        
        if (isset($parameters)) {
            foreach ($parameters as $key => $param) {
                $qb->andWhere('e.' . $key . ' = ?' . $index)->setParameter($index, $param);
                $index++;
            }
        }
       
        $q = $qb->getQuery();

        $objects = $q->execute();
        return $objects;
    }

    /**
     * This is the brain that updates data.
     * It updates given parameters and values, on records matching for field = Keys values
     */
    public function updateAllEntitiesByKeys(RTSentableInterface $entityInterface, array $parameters, String $field, array $keys)
    {
        $index = 1;
        $qb = $this->manager->createQueryBuilder();
        $qb->update($entityInterface->getName(), 'e');
        if (isset($parameters)) {
            foreach ($parameters as $key => $param) {
                $qb->set('e.' . $key, '?' . $index)->setParameter($index, $param);
                $index++;
            }
        }
        if (isset($keys)) {
            $qb->add('where', $qb->expr()->in('e.' . $field, ':v_keys'))
            ->setParameter('v_keys', $keys);
        }
        $q = $qb->getQuery();

        $objects = $q->execute();
        return $objects;
    }

    /**
     * This is the brain that performs queries on data tables
     */
    public function setOffAllEntitiesByParameters(RTSentableInterface $entityInterface, array $parameters = null)
    {
        $index = 1;
        $qb = $this->manager->createQueryBuilder();
    
        $qb->update($entityInterface->getName(), 'e')
                ->set('e.off','true')->where('1=1');
        
        if (isset($parameters)) {
            foreach ($parameters as $key => $param) {
                $qb->andWhere('e.' . $key . ' = ?' . $index)->setParameter($index, $param);
                $index++;
            }
        }
       
        $q = $qb->getQuery();

        $objects = $q->execute();
        return $objects;
    }

    /**
     * This is the brain that performs queries on data tables
     */
    public function setOffAllEntitiesByParametersIN(RTSentableInterface $entityInterface, array $parameters = null)
    {
        $index = 1;
        $qb = $this->manager->createQueryBuilder();
    
        $qb->update($entityInterface->getName(), 'e')
                ->set('e.off','true')->where('1=1');
        
        if (isset($parameters)) {
            foreach ($parameters as $key => $param) {
   
                $qb->andWhere('e.' . $key . ' IN (?' . $index.')')->setParameter($index, $param);
                $index++;
            }
        }
     
        $q = $qb->getQuery();
      

        $objects = $q->execute();
 
        return $objects;
    }

    /**
     * Retrieve ACTIVE entities by QUERY BUILDER.
     * (It filters for OFF flag as false or null. and sent = 0 for Sentable entities)
     */
    public function getActiveEntityObjects(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null) 
    {
        $objects = $this->mineEntities( $entityInterface, $parameters, $ordering, 0 );
        return $objects;
    }


     /**
     * Retrieve ACTIVE entities by QUERY BUILDER.
     * (It filters for OFF flag as false or null. and sent = 0 for Sentable entities)
     */
    public function getActiveEntityObjectsIN(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null) 
    {
        $objects = $this->mineEntitiesIN( $entityInterface, $parameters, $ordering, 0 );
        return $objects;
    }

    /**
     * Retrieve ALREADY SENT entities by QUERY BUILDER.
     * (It filters for OFF flag as false or null. and sent = 1 for Sentable entities)
     */
    public function getSentEntityObjects(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null) 
    {
        $objects = $this->mineEntities( $entityInterface, $parameters, $ordering, 1 );
        return $objects;
    }

    /**
     * It pops the top object of retrieved array.
     * It uses the same method getActiveEntityObjects filtering for first element.
     */
    public function popActiveEntity(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null) 
    {
        $results = $this->getActiveEntityObjects( $entityInterface, $parameters, $ordering );
        return array_pop( $results );
    }

    /**
     * It pops the top object of retrieved array.
     * It uses the same method getSentEntityObjects filtering for first element.
     */
    public function popSentEntity(RTSentableInterface $entityInterface, array $parameters = null, array $ordering = null) 
    {
        $results = $this->getSentEntityObjects( $entityInterface, $parameters, $ordering );
        return array_pop( $results );
    }

    /**
     * Retrieve entities by repository ClassType and Id value.
     * For example getEntityById( Rxsezioni::class, '234') returns record of table rxsezioni having id = 234
     */
    function getEntityById($classType, $id) 
    {
        $entityManager = $this->manager;
        $entity = $entityManager->getRepository($classType)->find($id);
        return $entity;
    }

    /**
     * [DEPRECATED] Retrieve ACTIVE entities by QUERY BUILDER.
     * (It filters for OFF flag as false or null.)
     * @deprecated from introduction of Off/Sent flags /divo/issues/30 use instead: getActiveEntityObjects( RTSentableInterface, prameters)
     */
    function getEntityObjects($entityName, $parameters = null) 
    {
        $qb = $this->manager->createQueryBuilder();
        $qb->select('e')
                ->from($entityName, 'e')
                ->where('e.off != ?1 OR e.off is null')->setParameter('1', 'true');
        if (isset($parameters)) {
            $iter = 2;
            foreach ($parameters as $key => $param) {
                $qb->andWhere('e.' . $key . ' = ?' . $iter)
                        ->setParameter($iter, $param);
                $iter++;
            }
        }
        $q = $qb->getQuery();

        $objects = $q->execute();
        return $objects;
    }
    
    /**
     * [DEPRECATED] It pops the top object of retrieved array.
     * It uses the same method get(Active)EntityObjects filtering for first element.
     * @deprecated from introduction of Off/Sent flags /divo/issues/30 use instead: popActiveEntity( RTSentableInterface, prameters)
     */
    public function getActiveEntityPop( $entityName, $parameters = null) 
    {
        $results = $this->getEntityObjects( $entityName, $parameters );
        return array_pop( $results );
    }

    /**
     * [DEPRECATED] Retrieve entities by repository and passing [] parameters to filter results
     * @deprecated from introduction of Off/Sent flags /divo/issues/30 use instead: getActiveEntityObjects( RTSentableInterface, parameters)
     */
    function getEntities($classType, $parameters, $ordering = null) 
    {
        $entityManager = $this->manager;
        $entities = $entityManager->getRepository($classType)->findBy($parameters, $ordering);
        return $entities;
    }

}
