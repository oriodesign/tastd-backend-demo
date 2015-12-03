<?php
namespace Tastd\Bundle\CoreBundle\Fixtures;

use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\String\Inflector;
use Tastd\Bundle\CoreBundle\String\MethodNamer;

/**
 * Class AbstractLoader
 *
 * @package Tastd\Bundle\CoreBundle\Fixtures
 */
class YamlFixturesLoader
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MethodNamer $methodNamer
     */
    protected $methodNamer;

    /**
     * @var Inflector $inflector
     */
    protected $inflector;

    /**
     * @param EntityManager $entityManager
     * @param MethodNamer   $methodNamer
     * @param Inflector     $inflector
     */
    public function __construct(EntityManager $entityManager, MethodNamer $methodNamer, Inflector $inflector)
    {
        $this->entityManager = $entityManager;
        $this->methodNamer = $methodNamer;
        $this->inflector = $inflector;
    }

    /**
     * @param string $path
     * @param string $name
     */
    public function loadFromYml($path, $name)
    {
        $data = $this->parse($path . $name . '.yml');
        $className = $data['class_name'];
        $this->resetAutoIncrement($className::SHORTCUT_CLASS_NAME);
        $this->persistObjects($data[$name], $className, $path);
        $this->entityManager->flush();
    }

    /**
     * @param array  $data
     * @param string $className
     * @param string $path
     */
    protected function persistObjects($data, $className, $path)
    {
        foreach ($data as $row) {
            $object = new $className();
            $this->setProperties($object, $row);
            $this->setBinaryContent($object, $row, $path);
            $this->entityManager->persist($object);
        }
    }

    /**
     * @param mixed $object
     * @param array $row
     */
    protected function setProperties($object, $row)
    {
        foreach ($row as $key => $value) {
            $method = $this->methodNamer->getSetMethodFromUnderscoreSingular($key);
            if (method_exists($object, $method)) {
                $this->setPropertyValue($object, $method, $value);
            }
        }
    }

    protected function setPropertyValue($object, $method, $value)
    {
        if (is_array($value) && array_key_exists('type', $value)) {
            $value = $this->getPropertyObjectValue($value);
        }
        $object->$method($value);
    }

    protected function getPropertyObjectValue($value)
    {
        $args = $value['arguments'];
        $type = $value['type'];
        $argumentsLenght = count($args);
        if ($argumentsLenght === 1) {
            return new $type($args[0]);
        } else if ($argumentsLenght === 2) {
            return new $type($args[0],$args[1]);
        } else if ($argumentsLenght === 3) {
            return new $type($args[0],$args[1],$args[2]);
        }
    }

    /**
     * @param mixed  $object
     * @param array  $row
     * @param string $path
     */
    protected function setBinaryContent($object, $row, $path)
    {
        if (isset($row['sonata_media_file'])) {
            $filename = $path.'media/'.$row['sonata_media_file'];
            $handle = fopen($filename, "rb");
            $uploaded = new UploadedFile($filename, $row['sonata_media_file']);
            $object->setBinaryContent($uploaded);

            fclose($handle);
        }
    }

    /**
     * @param string $shortcutClassName
     */
    protected function resetAutoIncrement($shortcutClassName)
    {
        $tableName = $this->entityManager->getClassMetadata($shortcutClassName)->getTableName();
        $connection = $this->entityManager->getConnection();
        $connection->exec('ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = 1;');
    }

    /**
     * @param string $path
     *
     * @return mixed $value
     */
    protected function parse($path)
    {
        $yaml = new Parser();
        $data = array();
        try {
            $data = $yaml->parse(file_get_contents($path));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        return $data;
    }


    /**
     * @param string $path
     * @param string $name
     */
    protected function loadRelationsFromYml($path, $name)
    {
        $data = $this->parse($path . $name . '.yml');
        $className = $data['class_name'];
        $this->loadRelations($data[$name], $className);
        $this->entityManager->flush();
    }

    /**
     * @param array  $data
     * @param string $className
     */
    protected function loadRelations($data, $className)
    {
        $i = 1;
        foreach ($data as $row) {
            if (isset($row["rel"])) {
                $repo = $this->entityManager->getRepository($className);
                $object = $repo->find($i);
                $rel = $row["rel"];
                if (isset($rel["many_to_many"])) {
                    $this->loadManyToMany($rel["many_to_many"], $object);
                }
                if (isset($rel["many_to_one"])) {
                    $this->loadManyToOne($rel["many_to_one"], $object);
                }
                if (isset($rel["one_to_many"])) {
                    $this->loadOneToMany($rel["one_to_many"], $object);
                }
            }
            $i ++;
        }
    }

    /**
     * @param array $data
     * @param mixed $object
     */
    protected function loadOneToMany($data, $object)
    {
        $metadataFactory = $this->entityManager->getMetadataFactory();
        $metadata = $metadataFactory->getMetadataFor(get_class($object));
        foreach ($data as $key => $value) {
            $associationClassName = $metadata->getAssociationTargetClass($this->inflector->underscoreToCamelCase($key));
            $associationRepository = $this->entityManager->getRepository($associationClassName);
            foreach ($value as $entityId) {
                $associatedEntity = $associationRepository->find($entityId);
                $method = $this->methodNamer->getAddMethodFromUnderscorePlural($key);
                $object->$method($associatedEntity);
            }
        }
    }

    /**
     * @param array $data
     * @param mixed $object
     */
    protected function loadManyToOne($data, $object)
    {
        $metadataFactory = $this->entityManager->getMetadataFactory();
        $metadata = $metadataFactory->getMetadataFor(get_class($object));
        foreach ($data as $key => $value) {
            $associationClassName = $metadata->getAssociationTargetClass($this->inflector->underscoreToCamelCase($key));
            $associationRepository = $this->entityManager->getRepository($associationClassName);
            $associatedEntity = $associationRepository->find($value);
            $method = $this->methodNamer->getSetMethodFromUnderscoreSingular($key);
            $object->$method($associatedEntity);
        }
    }

    /**
     * @param array $data
     * @param mixed $object
     */
    protected function loadManyToMany($data, $object)
    {
        $metadataFactory = $this->entityManager->getMetadataFactory();
        $metadata = $metadataFactory->getMetadataFor(get_class($object));
        foreach ($data as $key => $array) {
            $associationClassName = $metadata->getAssociationTargetClass($this->inflector->underscoreToCamelCase($key));
            $associationRepository = $this->entityManager->getRepository($associationClassName);
            $method = $this->methodNamer->getAddMethodFromUnderscorePlural($key);
            foreach ($array as $value) {
                $associatedEntity = $associationRepository->find($value);
                $object->$method($associatedEntity);
            }
        }
    }



}