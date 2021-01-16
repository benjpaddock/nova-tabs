<?php

declare(strict_types=1);

namespace Eminiarts\Tabs;

use RuntimeException;
use Laravel\Nova\Panel;
use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Contracts\ListableField;

class Tabs extends Panel
{
    /**
     * @var mixed
     */
    public $defaultSearch = false;

    /**
     * @var bool
     */
    public $showTitle = false;

    /**
     * Add fields to the Tab.
     *
     * @param string $tab
     * @param array  $fields
     * @return $this
     */
    public function addFields($tab, array $fields): self
    {
        foreach ($fields as $field) {
            if ($field instanceof ListableField || $field instanceof Panel) {
                $this->addTab($field);
                continue;
            }
            if ($field instanceof MergeValue) {
                $this->addFields($tab, $field->data);
                continue;
            }
            $field->panel = $this->name;
            $field->withMeta([
                'tab' => $tab,
            ]);
            $this->data[] = $field;
        }

        return $this;
    }

    /**
     * Add a new Tab
     *
     * @return $this
     */
    public function addTab($panel): self
    {
        if ($panel instanceof ListableField) {
            $panel->panel = $this->name;
            $panel->withMeta([
                'tab'         => $panel->name,
                'listable'    => false,
                'listableTab' => true,
            ]);
            $this->data[] = $panel;
        } elseif ($panel instanceof Panel) {
            $this->addFields($panel->name, $panel->data);
        } else {
            throw new RuntimeException('Only listable fields or Panel allowed.');
        }

        return $this;
    }

    /**
     * Show default Search if you need more space
     *
     * @param bool $value
     *
     * @return $this
     */
    public function defaultSearch(bool $value = true): self
    {
        $this->defaultSearch = $value;

        return $this;
    }

    /**
     * Whether the show the title
     *
     * @param bool $show
     * @return $this
     */
    public function showTitle($show = true): self
    {
        $this->showTitle = $show;

        return $this;
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'component'     => 'detail-tabs',
            'defaultSearch' => $this->defaultSearch,
            'showTitle' => $this->showTitle,
        ]);
    }

    /**
     * Prepare the given fields.
     *
     * @param  \Closure|array $fields
     * @return array
     */
    protected function prepareFields($fields)
    {
        collect(\is_callable($fields) ? $fields() : $fields)->each(function ($fields, $key): void {
            if (\is_string($key) && \is_array($fields)) {
                $fields = new Panel($key, $fields);
            }
            $this->addTab($fields);
        });

        return $this->data;
    }
}
