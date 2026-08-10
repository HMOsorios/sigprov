<?php

namespace App\Services;

use App\Models\NetworkElement;

class NetworkMapService
{
    public function getTree(): array
    {
        $roots = NetworkElement::whereNull('parent_id')
            ->where('status', 'active')
            ->with('childrenRecursive')
            ->orderBy('type')
            ->get()
            ->toArray();

        return $roots;
    }

    public function getClientPath(int $clientId): ?array
    {
        $clientElement = NetworkElement::where('type', 'client')
            ->where('identifier', (string) $clientId)
            ->first();

        if (!$clientElement) {
            return null;
        }

        $path = [];
        $current = $clientElement;

        while ($current) {
            $path[] = [
                'id' => $current->id,
                'name' => $current->name,
                'type' => $current->type,
                'type_label' => $current->type_label,
                'identifier' => $current->identifier,
            ];

            if (!$current->parent_id) {
                break;
            }

            $current = NetworkElement::find($current->parent_id);
        }

        return array_reverse($path);
    }

    public function getElementsByType(string $type): array
    {
        return NetworkElement::byType($type)
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'name', 'type', 'latitude', 'longitude', 'address', 'identifier'])
            ->toArray();
    }

    public function getClientCoordinates(int $clientId): ?array
    {
        $element = NetworkElement::where('type', 'client')
            ->where('identifier', (string) $clientId)
            ->first();

        if (!$element || !$element->latitude || !$element->longitude) {
            return null;
        }

        return [
            'lat' => (float) $element->latitude,
            'lng' => (float) $element->longitude,
        ];
    }

    public function getMapData(): array
    {
        $types = ['olt', 'splitter', 'cto', 'client'];

        $data = [];
        foreach ($types as $type) {
            $elements = $this->getElementsByType($type);
            if (!empty($elements)) {
                $data[$type] = $elements;
            }
        }

        return $data;
    }

    public function exportToGeoJson(): array
    {
        $elements = NetworkElement::where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $features = [];

        foreach ($elements as $element) {
            $color = match ($element->type) {
                'olt' => '#e74c3c',
                'splitter' => '#f39c12',
                'cto' => '#3498db',
                'client' => '#2ecc71',
                'backbone' => '#9b59b6',
                default => '#95a5a6',
            };

            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $element->longitude, (float) $element->latitude],
                ],
                'properties' => [
                    'id' => $element->id,
                    'name' => $element->name,
                    'type' => $element->type,
                    'type_label' => $element->type_label,
                    'identifier' => $element->identifier,
                    'address' => $element->address,
                    'marker-color' => $color,
                ],
            ];

            if ($element->parent_id) {
                $parent = NetworkElement::find($element->parent_id);
                if ($parent && $parent->latitude && $parent->longitude) {
                    $features[] = [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'LineString',
                            'coordinates' => [
                                [(float) $parent->longitude, (float) $parent->latitude],
                                [(float) $element->longitude, (float) $element->latitude],
                            ],
                        ],
                        'properties' => [
                            'type' => 'connection',
                            'from' => $parent->name,
                            'to' => $element->name,
                            'stroke' => '#95a5a6',
                            'stroke-width' => 1,
                        ],
                    ];
                }
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }
}
