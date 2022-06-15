<?php
/** @var string $assetBasePath */
/** @var string $url */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
        name="description"
        content="SwaggerUI"
    />
    <title>SwaggerUI</title>
    <link rel="stylesheet" href="<?= $assetBasePath ?>/swagger-ui.css" />
</head>
<body>
<div id="swagger-ui"></div>
<script src="<?= $assetBasePath ?>/swagger-ui-bundle.js" crossorigin></script>
<script>
    window.onload = () => {
        window.ui = SwaggerUIBundle({
            // @link https://github.com/swagger-api/swagger-ui/blob/master/docs/usage/configuration.md
            dom_id: '#swagger-ui',
            url: '<?= $url ?>',
            filter: '',
        });
    };
</script>
</body>
</html>
