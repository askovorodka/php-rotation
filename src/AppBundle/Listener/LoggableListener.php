<?php

namespace AppBundle\Listener;

use AppBundle\Entity\LogEntry;
use Doctrine\ORM\UnitOfWork;
use Gedmo\Loggable\LoggableListener as BaseListener;
use Doctrine\Common\EventArgs;
use Gedmo\Mapping\Event\AdapterInterface;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Persistence\Proxy;

/**
 * Class LoggableListener
 */
class LoggableListener extends BaseListener
{
    /**
     * @var AdapterInterface
     */
    protected $eventAdapter;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Set username for identification
     *
     * @param mixed $user
     *
     * @throws \Gedmo\Exception\InvalidArgumentException Invalid username
     */
    public function setUsername($user)
    {
        if ($user instanceof UserInterface) {
            $this->user = $user;
        }

        parent::setUsername($user);
    }

    /**
     * Looks for loggable objects being inserted or updated
     * for further processing
     *
     * @param EventArgs $eventArgs
     *
     * @return void
     */
    public function onFlush(EventArgs $eventArgs)
    {
        $this->eventAdapter = $this->getEventAdapter($eventArgs);

        parent::onFlush($eventArgs);
    }

    /**
     * Handle any custom LogEntry functionality that needs to be performed
     * before persisting it
     *
     * @param LogEntry $logEntry The LogEntry being persisted
     * @param object $object The object being Logged
     */
    protected function prePersistLogEntry($logEntry, $object)
    {
        if ($this->user instanceof UserInterface) {
            $logEntry->setUser($this->user);
        }

        if ($this->eventAdapter) {
            $om = $this->eventAdapter->getObjectManager();
            /** @var UnitOfWork $uow */
            $uow = $om->getUnitOfWork();
            $wrapped = AbstractWrapper::wrap($object, $om);
            $meta = $wrapped->getMetadata();
            $config = $this->getConfiguration($om, $meta->name);

            if (
                $logEntry->getAction() !== self::ACTION_CREATE
                && $logEntry->getOldData() === null
            ) {
                if (!empty($config['versioned'])) {
                    $oldValues = [];
                    $changeSet = $uow->getEntityChangeSet($object);

                    foreach ($changeSet as $field => $changes) {
                        if (empty($config['versioned']) || !in_array($field, $config['versioned'], true)) {
                            continue;
                        }

                        if (!array_key_exists(0, $changes)) {
                            continue;
                        }
                        $value = $changes[0];
                        $oldValues[$field] = $this->getVersionedValue($logEntry, $object, $field, $value);
                    }

                    if ($oldValues) {
                        $logEntry->setOldData($oldValues);
                    }

                    // save object data when remove
                    if (
                        $logEntry->getAction() == self::ACTION_REMOVE
                        && $logEntry->getData() === null
                    ) {
                        $origData = $uow->getOriginalEntityData($object);

                        if ($origData) {
                            $values = [];
                            foreach ($origData as $field => $value) {
                                if (!in_array($field, $config['versioned'], true)) {
                                    continue;
                                }

                                $values[$field] = $this->getVersionedValue($logEntry, $object, $field, $value);
                            }

                            if ($values) {
                                $logEntry->setData($values);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param LogEntry $logEntry
     * @param object $object
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    private function getVersionedValue($logEntry, $object, $field, $value)
    {
        if ($value) {
            $om = $this->eventAdapter->getObjectManager();
            $wrapped = AbstractWrapper::wrap($object, $om);
            $meta = $wrapped->getMetadata();

            if ($meta->isSingleValuedAssociation($field)) {
                if ($wrapped->isEmbeddedAssociation($field)) {
                    $value = $this->getObjectChangeSetData($this->eventAdapter, $value, $logEntry);
                } else {
                    $wrappedAssoc = AbstractWrapper::wrap($value, $om);
                    $value = $wrappedAssoc->getIdentifier(false);
                    if (!is_array($value) && !$value) {
                        return $value;
                    }
                }
            } elseif ($value instanceof Proxy) {
                $value = ['id' => $value->getId()];
            }
        }

        return $value;
    }
}
