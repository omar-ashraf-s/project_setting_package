<?php

namespace Mabrouk\ProjectSetting\Http\Requests\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\ProjectSetting\Models\ProjectSettingSection;

class ProjectSettingSectionStoreRequest extends FormRequest
{
    public ProjectSettingSection $projectSettingSection;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:191',
            'description' => 'required|string|min:2|max:191',
        ];
    }

    public function storeProjectSettingSection()
    {
        DB::transaction(function () {
            $this->projectSettingSection = $this->project_setting_group->projectSettingSections()->create();
        });
        return $this->projectSettingSection->refresh();
    }

    public function attributes(): array
    {
        return [
            'name' => __('mabrouk/project_settings/project_setting_sections.attributes.name'),
            'description' => __('mabrouk/project_settings/project_setting_sections.attributes.description'),
        ];
    }
}
