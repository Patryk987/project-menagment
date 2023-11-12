<?php

namespace Notes\Model;

class FieldsModel
{
    private int $filed_id;
    private int $project_id;
    private string $field_name;
    private string $type;
    private string $value;

    public function __construct(
        int $filed_id,
        int $project_id,
        string $field_name,
        string $type,
        string $value
    ) {
        $this->filed_id = $filed_id;
        $this->project_id = $project_id;
        $this->field_name = $field_name;
        $this->type = $type;
        $this->value = $value;
    }

    // SET
    public function set_filed_id(int $filed_id): void
    {
        $this->filed_id = $filed_id;
    }
    public function set_project_id(int $project_id): void
    {
        $this->project_id = $project_id;
    }
    public function set_field_name(string $field_name): void
    {
        $this->field_name = $field_name;
    }
    public function set_type(string $type): void
    {
        $this->type = $type;
    }
    public function set_value(string $value): void
    {
        $this->value = $value;
    }

    // GET

    public function get_filed_id(): int
    {
        return $this->filed_id;
    }
    public function get_project_id(): int
    {
        return $this->project_id;
    }
    public function get_field_name(): string
    {
        return $this->field_name;
    }
    public function get_type(): string
    {
        return $this->type;
    }
    public function get_value(): string
    {
        return $this->value;
    }
}
