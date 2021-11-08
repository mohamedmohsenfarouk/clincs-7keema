<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * 7keema - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Services Model
 *
 * @package Models
 */
class Branches_model extends EA_Model {
    /**
     * Branches_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('data_validation');
    }

    /**
     * Add (insert or update) a service record on the database
     *
     * @param array $branch Contains the service data. If an 'id' value is provided then the record will be updated.
     *
     * @return int Returns the record id.
     * @throws Exception
     */
    public function add($branch)
    {
        $this->validate($branch);

        if ( ! isset($branch['id']))
        {
            $branch['id'] = $this->insert($branch);
        }
        else
        {
            $this->update($branch);
        }

        return (int)$branch['id'];
    }

    /**
     * Validate a service record data.
     *
     * @param array $branch Contains the service data.
     *
     * @return bool Returns the validation result.
     *
     * @throws Exception If service validation fails.
     */
    public function validate($branch)
    {
        // If record id is provided we need to check whether the record exists in the database.
        if (isset($branch['id']))
        {
            $num_rows = $this->db->get_where('branches', ['id' => $branch['id']])->num_rows();

            if ($num_rows == 0)
            {
                throw new Exception('Provided service id does not exist in the database.');
            }
        }

        // Check if service category id is valid (only when present).
        if ( ! empty($branch['id_service_categories']))
        {
            $num_rows = $this->db->get_where('service_categories',
                ['id' => $branch['id_service_categories']])->num_rows();
            if ($num_rows == 0)
            {
                throw new Exception('Provided service category id does not exist in database.');
            }
        }

        // Check for required fields
        if ($branch['name'] == '')
        {
            throw new Exception('Not all required branch fields where service: '
                . print_r($branch, TRUE));
        }
        
        if ($branch['location'] == '')
        {
            throw new Exception('Not all required branch fields where service location: '
                . print_r($branch, TRUE));
        }

        return TRUE;
    }

    /**
     * Insert service record into database.
     *
     * @param array $branch Contains the service record data.
     *
     * @return int Returns the new service record id.
     *
     * @throws Exception If service record could not be inserted.
     */
    protected function insert($branch)
    {
        if ( ! $this->db->insert('branches', $branch))
        {
            throw new Exception('Could not insert branch record.');
        }
        return (int)$this->db->insert_id();
    }

    /**
     * Update service record.
     *
     * @param array $branch Contains the service data. The record id needs to be included in the array.
     *
     * @throws Exception If service record could not be updated.
     */
    protected function update($branch)
    {
        $this->db->where('id', $branch['id']);
        if ( ! $this->db->update('branches', $branch))
        {
            throw new Exception('Could not update branch record');
        }
    }

    /**
     * Checks whether an service record already exists in the database.
     *
     * @param array $branch Contains the service data. Name, duration and price values are mandatory in order to
     * perform the checks.
     *
     * @return bool Returns whether the service record exists.
     *
     * @throws Exception If required fields are missing.
     */
    public function exists($branch)
    {
        if ( ! isset(
            $branch['name'],
            $branch['location']
        ))
        {
            throw new Exception('Not all branch fields are service in order to check whether '
                . 'a branch record already exists: ' . print_r($branch, TRUE));
        }

        $num_rows = $this->db->get_where('branches', [
            'name' => $branch['name'],
            'location' => $branch['location']
        ])->num_rows();

        return $num_rows > 0;
    }

    /**
     * Get the record id of an existing record.
     *
     * Notice: The record must exist, otherwise an exception will be raised.
     *
     * @param array $branch Contains the service record data. Name, duration and price values are mandatory for this
     * method to complete.
     *
     * @return int
     *
     * @throws Exception If required fields are missing.
     * @throws Exception If requested service was not found.
     */
    public function find_record_id($branch)
    {
        if ( ! isset($branch['name'])
            || ! isset($branch['location'])
        )
        {
            throw new Exception('Not all required fields where service in order to find the '
                . 'branch record id.');
        }

        $result = $this->db->get_where('branches', [
            'name' => $branch['name'],
            'location' => $branch['location']
        ]);

        if ($result->num_rows() == 0)
        {
            throw new Exception('Could not find branch record id');
        }

        return $result->row()->id;
    }

    /**
     * Delete a service record from database.
     *
     * @param int $branch_id Record id to be deleted.
     *
     * @return bool Returns the delete operation result.
     *
     * @throws Exception If $branch_id argument is invalid.
     */
    public function delete($branch_id)
    {
        if ( ! is_numeric($branch_id))
        {
            throw new Exception('Invalid argument type $branch_id (value:"' . $branch_id . '"');
        }

        $num_rows = $this->db->get_where('branches', ['id' => $branch_id])->num_rows();
        if ($num_rows == 0)
        {
            return FALSE; // Record does not exist
        }

        return $this->db->delete('branches', ['id' => $branch_id]);
    }

    /**
     * Get a specific row from the services db table.
     *
     * @param int $branch_id The record's id to be returned.
     *
     * @return array Returns an associative array with the selected record's data. Each key has the same name as the
     * database field names.
     *
     * @throws Exception If $branch_id argument is not valid.
     */
    public function get_row($branch_id)
    {
        if ( ! is_numeric($branch_id))
        {
            throw new Exception('$branch_id argument is not an numeric (value: "' . $branch_id . '")');
        }
        return $this->db->get_where('branches', ['id' => $branch_id])->row_array();
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field_name The field name of the value to be
     * returned.
     * @param int $branch_id The selected record's id.
     *
     * @return string Returns the records value from the database.
     *
     * @throws Exception If $branch_id argument is invalid.
     * @throws Exception If $field_name argument is invalid.
     * @throws Exception if requested service does not exist in the database.
     * @throws Exception If requested field name does not exist in the database.
     */
    public function get_value($field_name, $branch_id)
    {
        if ( ! is_numeric($branch_id))
        {
            throw new Exception('Invalid argument provided as $branch_id: ' . $branch_id);
        }

        if ( ! is_string($field_name))
        {
            throw new Exception('$field_name argument is not a string: ' . $field_name);
        }

        if ($this->db->get_where('branches', ['id' => $branch_id])->num_rows() == 0)
        {
            throw new Exception('The record with the $branch_id argument does not exist in the database: ' . $branch_id);
        }

        $row_data = $this->db->get_where('branches', ['id' => $branch_id])->row_array();

        if ( ! array_key_exists($field_name, $row_data))
        {
            throw new Exception('The given $field_name argument does not exist in the database: '
                . $field_name);
        }

        return $row_data[$field_name];
    }

    /**
     * Get all, or specific records from service's table.
     *
     * Example:
     *
     * $this->Branches_model->get_batch(['id' => $record_id]);
     *
     * @param mixed $where
     * @param int|null $limit
     * @param int|null $offset
     * @param mixed $order_by
     *
     * @return array Returns the rows from the database.
     */
    public function get_batch($where = NULL, $limit = NULL, $offset = NULL, $order_by = NULL)
    {
        if ($where !== NULL)
        {
            $this->db->where($where);
        }

        if ($order_by !== NULL)
        {
            $this->db->order_by($order_by);
        }

        return $this->db->get('branches', $limit, $offset)->result_array();
    }

    /**
     * This method returns all the branches from the database.
     *
     * @return array Returns an object array with all the database branches.
     */
    public function get_available_branches()
    {
        $this->db->distinct();

        return $this->db
            ->select('branches.* , services.id as service_id, 
                    services.name as service_name')
            ->from('branches')
            ->join('services_branches',
                'services_branches.id_branches = branches.id', 'inner')
            ->join('services',
                'services_branches.id_services = services.id', 'inner')
            ->group_by('services_branches.id_branches')
            ->order_by('name ASC')
            ->get()->result_array();
    }

}
