<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\User\Mappers;

use Modules\User\Models\Group as GroupModel;

class Group extends \Ilch\Mapper
{
    /**
     * Returns user model found by the id or false if none found.
     *
     * @param  int $id
     * @return null|GroupModel
     */
    public function getGroupById($id)
    {
        $where = [
            'id' => (int) $id,
        ];

        $groups = $this->getBy($where);

        if (!empty($groups)) {
            return reset($groups);
        }

        return null;
    }

    /**
     * Returns user model found by the name or false if none found.
     *
     * @param  string           $name
     * @return false|GroupModel
     */
    public function getGroupByName($name)
    {
        $where = [
            'name' => $name,
        ];

        $groups = $this->getBy($where);

        if (!empty($groups)) {
            return reset($groups);
        }

        return false;
    }

    /**
     * Returns an array with user group models found by the where clause of false if
     * none found.
     *
     * @param  mixed[]            $where
     * @return false|GroupModel[]
     */
    protected function getBy($where = [])
    {
        $groupRows = $this->db()->select('*')
            ->from('groups')
            ->where($where)
            ->execute()
            ->fetchRows();

        if (!empty($groupRows)) {
            return array_map([$this, 'loadFromArray'], $groupRows);
        }

        return false;
    }

    /**
     * Returns a user group created using an array with user group data.
     *
     * @param  mixed[]    $groupRow
     * @return GroupModel
     */
    public function loadFromArray($groupRow = [])
    {
        $group = new GroupModel();

        if (isset($groupRow['id'])) {
            $group->setId($groupRow['id']);
        }

        if (isset($groupRow['name'])) {
            $group->setName($groupRow['name']);
        }

        return $group;
    }

    /**
     * Loads the user ids associated with a user group.
     *
     * @param int $groupId
     * @return string[]
     */
    public function getUsersForGroup($groupId)
    {
        return $this->db()->select('user_id', 'users_groups', ['group_id' => $groupId])
            ->execute()
            ->fetchList();
    }

    /**
     * Inserts or updates a user group model into the database.
     *
     * @param GroupModel $group
     * @return int
     */
    public function save(GroupModel $group)
    {
        $fields = [];
        $name = $group->getName();

        if (!empty($name)) {
            $fields['name'] = $group->getName();
        }

        $groupId = (int) $this->db()->select('id', 'groups', ['id' => $group->getId()])
            ->execute()
            ->fetchCell();

        if ($groupId) {
            /*
             * Group does exist already, update.
             */
            $this->db()->update('groups')
                ->values($fields)
                ->where(['id' => $groupId])
                ->execute();
        } else {
            /*
             * Group does not exist yet, insert.
             */
            $groupId = $this->db()->insert('groups')
                ->values($fields)
                ->execute();
        }

        return $groupId;
    }

    /**
     * Returns a array of all group model objects.
     *
     * @return groupModel[]
     */
    public function getGroupList()
    {
        return $this->getBy();
    }

    /**
     * Returns whether a group with the given id exists in the database.
     *
     * @param  int $groupId
     * @return boolean
     */
    public function groupWithIdExists($groupId)
    {
        return (boolean) $this->db()->select('COUNT(*)', 'groups', ['id' => (int)$groupId])
            ->execute()
            ->fetchCell();
    }

    /**
     * Deletes a given group or a user with the given id.
     *
     * @param  int|GroupModel $groupId
     *
     * @return boolean True of success, otherwise false.
     */
    public function delete($groupId)
    {
        if (is_a($groupId, GroupModel::class)) {
            $groupId = $groupId->getId();
        }

        $this->db()->delete('users_groups')
            ->where(['group_id' => $groupId])
            ->execute();

        return $this->db()->delete('groups')
            ->where(['id' => $groupId])
            ->execute();
    }

    /**
     * Returns the group access list from the database.
     *
     * @param  int $groupId
     * @return mixed[]
     * @throws \Ilch\Database\Exception
     */
    public function getGroupAccessList($groupId)
    {
        $sql = 'SELECT g.name AS group_name, ga.*
                FROM [prefix]_groups_access AS ga
                INNER JOIN [prefix]_groups AS g ON ga.group_id = g.id
                WHERE ga.group_id = '.(int)$groupId;
        $accessDbList = $this->db()->queryArray($sql);
        $accessList = [];
        $accessList['entries'] = [
            'page' => [],
            'module' => [],
            'article' => [],
            'box' => [],
        ];
        $accessTypes = [
            'module',
            'page',
            'article',
            'box',
        ];

        foreach ($accessDbList as $accessDbListEntry) {
            if (empty($accessList['group_name'])) {
                /*
                 * Only on first entry.
                 */
                $accessList['group_name'] = $accessDbListEntry['group_name'];
            }

            foreach ($accessTypes as $accessType) {
                if (!empty($accessDbListEntry[$accessType.'_id'])) {
                    $accessList['entries'][$accessType][$accessDbListEntry[$accessType.'_id']] = $accessDbListEntry['access_level'];
                    break;
                }
                
                if (!empty($accessDbListEntry[$accessType.'_key'])) {
                    $accessList['entries'][$accessType][$accessDbListEntry[$accessType.'_key']] = $accessDbListEntry['access_level'];
                    break;
                }
            }
        }

        return $accessList;
    }
    
    /**
     * Returns the access access list from the database.
     *
     * @param  string $Type
     * @param  string $Id
     * @return mixed[]
     * @throws \Ilch\Database\Exception
     */
    public function getAccessAccessList($Type, $Id)
    {
        $sqlwhere = '';
        if ($Type === 'module') {
            $sqlwhere = 'module_key';
        } elseif ($Type === 'article') {
            $sqlwhere = 'article_id';
        } elseif ($Type === 'page') {
            $sqlwhere = 'page_id';
        } elseif ($Type === 'box') {
            $sqlwhere = 'box_id';
        }

        if ($sqlwhere == '') {
            return null;
        }

        $sql = 'SELECT g.name AS group_name, ga.*
                FROM [prefix]_groups_access AS ga
                INNER JOIN [prefix]_groups AS g ON ga.group_id = g.id
                WHERE ga.'.$sqlwhere.' = "'.$Id.'"';
        $accessDbList = $this->db()->queryArray($sql);
        $accessList = [];
        $accessList['entries'] = [];

        $accessList['Type'] = $Type;
        $accessList['Id'] = $Id;

        foreach ($accessDbList as $accessDbListEntry) {
            $accessList['entries'][$accessDbListEntry['group_id']] = (int)$accessDbListEntry['access_level'];
        }
        return $accessList;
    }

    /**
     * Saves or updates an access data entry to the db.
     *
     * @param  int    $groupId
     * @param  mixed  $value
     * @param  int    $accessLevel
     * @param  string $type
     */
    public function saveAccessData($groupId, $value, $accessLevel, $type)
    {
        $rec = [
            'group_id' => (int)$groupId,
        ];

        if ($type === 'module') {
            $rec['module_key'] = $value;
        } else {
            $rec[$type.'_id'] = (int)$value;
        }

        $fields = $rec;
        $fields['access_level'] = (int)$accessLevel;
        $entryExists = (bool)$this->db()->select('COUNT(*)')
            ->from('groups_access')
            ->where($rec)
            ->execute()
            ->fetchCell();

        if ($entryExists) {
            $this->db()->update('groups_access')
                ->values($fields)
                ->where($rec)
                ->execute();
        } else {
            $this->db()->insert('groups_access')
                ->values($fields)
                ->execute();
        }
    }
}
