<?php
namespace Bitfalls\Traits;
use Bitfalls\Phalcon\Model;
use Phalcon\DI;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Mvc\Model\Query;

/**
 * Class TagAware
 */
trait TagAware
{

    /** @var array */
    protected $tag_binds;

    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getTagBinds()
    {
        $this->check();
        if ($this->tag_binds === null) {
            $this->tag_binds = \TagBind::find(array('entity_type = :et: AND entity_id = :eid:', 'bind' => array('et' => $this->entity_type, 'eid' => $this->getId())));
        }
        return $this->tag_binds;
    }

    /**
     * @param $tag
     * @return bool
     */
    public function hasTag($tag)
    {
        /** @var \TagBind $oTagBind */
        foreach ($this->getTagBinds() as $oTagBind) {

            //die(var_dump($oTagBind->tags));
            if ($oTagBind->tags->getTag() == $tag) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $sType
     * @return array
     */
    public function getTagBindsByType($sType)
    {
        $this->check();
        $m = new Manager();
        $m->setDI($this->getDI());
        $b = $m->createBuilder();

        $aResult = $b->from('TagBind')
            ->join('Tags', 'Tags.id = TagBind.tag')
            ->join('TagTypes', 'TagTypes.id = Tags.tag_type')
            ->where('TagTypes.type = :t: AND TagBind.entity_type = :et: AND TagBind.entity_id = :ei:')
            ->getQuery()
            ->execute(
                array(
                    't' => $sType,
                    'et' => $this->entity_type,
                    'ei' => $this->getId()
                )
            );

        return $aResult;
    }

    /**
     * Returns an array of tag dummies for rendering in, for example, AngularJS
     * @return array
     */
    public function getTagsArray()
    {
        $aReturn = array();
        /** @var \TagBind $oTagBind */
        foreach ($this->getTagBinds() as $oTagBind) {
            $oTagObject = $oTagBind->tags->getDummy();
            $oTagObject->value = $oTagBind->getValue();
            $oTagObject->possibleValues = $oTagBind->tags->getPossibleValues();
            $aReturn[] = $oTagObject;
        }
        return $aReturn;
    }

    /**
     * @param $tag
     * @return bool|string
     */
    public function getTagValue($tag)
    {
        /** @var \TagBind $oTagBind */
        foreach ($this->getTagBinds() as $oTagBind) {
            if ($oTagBind->tags->getTag() == $tag) {
                return $oTagBind->getValue();
            }
        }
        return false;
    }

    protected function check()
    {
        if (empty($this->entity_type)) {
            throw new \Exception('Cannot use class as TagAware if entity_type is not defined.');
        }

    }

    public abstract function getId();

}