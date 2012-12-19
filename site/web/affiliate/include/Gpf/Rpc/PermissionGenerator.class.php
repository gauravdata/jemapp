<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: PermissionDeniedException.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Rpc_PermissionGenerator extends Gpf_Object {
    private $dirs = array();

    private $frameworkPrivileges = array();
    private $applicationPrivileges = array();

    private $frameworkPrivilegeTypes = array();
    private $applicationPrivilegeTypes = array();

    private $frameworkPrivilegesClass = 'Gpf_Privileges';
    private $frameworkClientPrivilegesClass = 'com.qualityunit.gpf.client.model.Permissions';
    private $applicationPrivilegesClass;
    private $applicationClientPrivilegesClass;

    const TAG = '<PRIVILEGES_START>';

    public function __construct($applicationPrivilegesClass, $applicationClientPrivilegesClass = '') {
        $this->dirs = Gpf_Paths::getInstance()->getIncludePaths();
        $this->applicationPrivilegesClass = $applicationPrivilegesClass;
        $this->applicationClientPrivilegesClass = $applicationClientPrivilegesClass;
    }

    public function generate() {
        foreach ($this->dirs as $directory) {
            $iterator = new Gpf_Io_DirectoryIterator($directory, '.php', true);
            $iterator->addIgnoreDirectory('.svn');
            foreach ($iterator as $fullName => $name) {
                $file = new Gpf_Io_File($fullName);
                $count = preg_match_all('/@service\s+([a-zA-Z_]+)\s+([a-zA-Z_]+)/ms',
                $file->getContents(), $matches, PREG_OFFSET_CAPTURE);
                if ($count > 0) {
                    foreach ($matches[1] as $index => $sentence) {
                        if (strpos($directory, "GwtPhpFramework")) {
                            $this->addFrameworkPermission($matches[1][$index][0], $matches[2][$index][0]);
                        } else {
                            $this->addApplicationPermission($matches[1][$index][0], $matches[2][$index][0]);
                        }
                    }
                }
            }
        }
        $this->renderOutput();
    }

    private function addFrameworkPermission($object, $privillege) {
        $this->addPermission($object, $privillege,
        $this->frameworkPrivileges,
        $this->frameworkPrivilegeTypes);
    }

    private function addApplicationPermission($object, $privillege) {
        $this->addPermission($object, $privillege,
        $this->applicationPrivileges,
        $this->applicationPrivilegeTypes);
    }

    private function addPermission($object, $privillege, &$privilegeList, &$privilegeTypes) {
        if (!array_key_exists($privillege, $privilegeTypes)) {
            $privilegeTypes[$privillege] = $privillege;
        }
        if(!array_key_exists($object, $privilegeList)) {
            $privileges = array();
            $privileges[$privillege] = $privillege;
            $privilegeList[$object] = $privileges;
            return;
        }
        $privileges = $privilegeList[$object];
        if(!array_key_exists($privillege, $privileges)) {
            $privilegeList[$object][$privillege] = $privillege;
        }
    }

    private function renderOutput() {
        ksort($this->frameworkPrivileges);
        ksort($this->applicationPrivileges);
        $this->renderPrivileges($this->frameworkPrivilegesClass, $this->frameworkPrivileges, $this->frameworkPrivilegeTypes, false);
        $this->renderClientPrivileges('../../../../GwtPhpFramework/trunk/gpf/src/', $this->frameworkClientPrivilegesClass, $this->frameworkPrivileges);

        if ($this->applicationPrivilegesClass) {
            $this->renderPrivileges($this->applicationPrivilegesClass, $this->applicationPrivileges, $this->applicationPrivilegeTypes, true);
            if ($this->applicationClientPrivilegesClass != '') {
                $this->renderClientPrivileges('../../client/src/', $this->applicationClientPrivilegesClass, $this->applicationPrivileges);
            }
        }
    }

    private function renderClientPrivileges($path, $className, $privilegeList) {
        $lastDot = strrpos($className, '.');
        $package = substr($className, 0, $lastDot);
        $class = substr($className, $lastDot+1);

        $imports = '';
        if (!strpos($package, 'gpf')) {
            $imports = '
import com.qualityunit.gpf.client.model.Application;
import com.qualityunit.gpf.client.model.Permission;
';
        }

        $permissionVariables = '';
        foreach ($privilegeList as $object => $privileges) {
            foreach ($privileges as $privilege) {
                $permissionVariables .= '  ' . strtoupper($object). '_' . strtoupper($privilege) . "(\"$object|$privilege\"),\n";
            }
        }
        $permissionVariables = rtrim($permissionVariables, " ,'\n").';';

        $content = "/**
 * Copyright 2007 Quality Unit s.r.o.
 *
 * Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 * Version 1.0 (the \"License\"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 * http://www.qualityunit.com/licenses/license
 *
 */

package $package;
$imports
public enum $class implements Permission {
$permissionVariables

  private String permission;

  private $class(String permission) {
    this.permission = permission;
  }

  public boolean isAllowed() {
    return Application.hasPermission(permission);
  }
}";
        $file = new Gpf_Io_File($path . str_replace('.', '/', $className) . '.java');
        $file->putContents($content);
    }

    private function renderPrivileges($className, $privilegeList, $privilegeTypes, $hasParentClass) {
        if ($fileName = Gpf::existsClass($className)) {
            $file = new Gpf_Io_File($fileName);
            $file->setFileMode('r');
            $content = $file->getContents();
            $file->close();

            if (($pos = strpos($content, self::TAG)) === false) {
                throw new Gpf_Exception('Missing tag ' . self::TAG . ' in privileges class ' . $className . ' !');
            }

            $file = new Gpf_Io_File($fileName);
            $file->open('w');
            $file->write(substr($content, 0, $pos + strlen(self::TAG)) . "\n\n");

            $file->write("\t// Privilege types\n");
            foreach ($privilegeTypes as $privilege) {
                $file->write("\t" . 'const ' . $this->formatPrivilegeType($privilege) . ' = "' . $privilege . '";' . "\n");
            }
            $file->write("\n");

            $file->write("\t// Privilege objects\n");
            foreach ($privilegeList as $object => $privileges) {
                ksort($privileges);
                $privilegeTypeComments = '// ';
                foreach ($privileges as $privilege) {
                    $privilegeTypeComments .= $this->formatPrivilegeType($privilege) . ', ';
                }
                $privilegeTypeComments = rtrim($privilegeTypeComments, ", ");
                $file->write("\t" . 'const ' . strtoupper($object) . ' = "' . $object . '"; ' .
                $privilegeTypeComments . "\n");
            }

            $file->write("\t\n\n\tprotected function initObjectRelation() {\n\t\t");
            if ($hasParentClass) {
                $file->write('$objectRelation = array_merge_recursive(parent::initObjectRelation(), array(');
            } else {
                $file->write('return array(');
            }
            $comma = "\n\t\t";
            foreach ($privilegeList as $object => $privileges) {
                $file->write($comma . "self::" . strtoupper($object) . "=>array(");
                ksort($privileges);
                $privilegeTypes = '';
                foreach ($privileges as $privilege) {
                    $privilegeTypes .= 'self::' . $this->formatPrivilegeType($privilege) . ', ';
                }
                $file->write(rtrim($privilegeTypes, ", ") . ")");
                $comma = ",\n\t\t";
            }
            $file->write("\n\t\t)" . ($hasParentClass ? ");\r\r\t\tforeach (\$objectRelation as \$key => \$value) {\r\t\t\t\$objectRelation[\$key] = array_unique(\$value);\r\t\t}\r\t\treturn \$objectRelation;" : ';') . "\r\t}\n");

            $file->write("\n}\n?>");
        }
    }

    private function formatPrivilegeType($privilege) {
        return 'P_'.strtoupper($privilege);
    }
}
?>
