<?php
return [
    'adminEmail' => 'admin@example.com',

    'maxFileSize' => 1024 * 1024 * 2, // 2 megabytes
    'storagePath' => '@frontend/web/uploads/',
    'storageUri' => '/uploads/', // http://images.com/uploads/f1/d7/739f9a9c9a99294.jpg

    // Настройки для изображений
    'profilePicture' => [
        'maxWidth' => 200,
        'maxHeight' => 200,
    ],
    'postPicture' => [
        'maxWidth' => 1024,
        'maxHeight' => 768,
    ],

    'feedPostLimit' => 200,
    'profilePostLimit' => 50,
];
