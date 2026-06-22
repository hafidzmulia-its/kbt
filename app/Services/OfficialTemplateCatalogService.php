<?php

namespace App\Services;

use App\Models\Template;

class OfficialTemplateCatalogService
{
    private const OFFICIAL_TEMPLATES = [
        [
            'code' => 'valley-of-blue',
            'name' => 'Valley of Blue',
            'category' => 'standard',
            'preview_image_path' => 'template-previews/valley-of-blue.svg',
            'status' => 'active',
            'is_premium' => false,
        ],
        [
            'code' => 'cormorant-gold',
            'name' => 'Cormorant Gold',
            'category' => 'official',
            'preview_image_path' => 'template-previews/cormorant-gold.svg',
            'status' => 'active',
            'is_premium' => false,
        ],
        [
            'code' => 'playfair-blush',
            'name' => 'Playfair Blush',
            'category' => 'official',
            'preview_image_path' => 'template-previews/playfair-blush.svg',
            'status' => 'active',
            'is_premium' => false,
        ],
        [
            'code' => 'graduation-elegance',
            'name' => 'Graduation Elegance',
            'category' => 'official',
            'preview_image_path' => 'template-previews/graduation-elegance.svg',
            'status' => 'active',
            'is_premium' => false,
        ],
    ];

    public function sync(): void
    {
        foreach (self::OFFICIAL_TEMPLATES as $template) {
            $model = Template::query()->firstOrNew(
                ['code' => $template['code']],
            );

            if (! $model->exists) {
                $model->fill($template)->save();
                continue;
            }

            if (blank($model->preview_image_path)) {
                $model->preview_image_path = $template['preview_image_path'];
                $model->save();
            }
        }
    }
}
