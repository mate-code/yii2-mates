<?php
/** @var \dosamigos\fileupload\FileUploadUI $this */
use yii\helpers\Html;

$context = $this->context;
?>
<!-- The file upload form used as target for the file upload widget -->
<?= Html::beginTag('div', $context->options); ?>
<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
<div class="row fileupload-buttonbar">
    <div class="col-md-6">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-primary fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span><?= Yii::t('fileupload', 'Add files') ?>...</span>

            <?= $context->model instanceof \yii\base\Model && $context->attribute !== null
                ? Html::activeFileInput($context->model, $context->attribute, $context->fieldOptions)
                : Html::fileInput($context->name, $context->value, $context->fieldOptions); ?>

            </span>
        <a class="btn btn-primary start">
            <i class="glyphicon glyphicon-upload"></i>
            <span><?= Yii::t('fileupload', 'Start upload') ?></span>
        </a>
        <!-- The global file processing state -->
        <span class="fileupload-process"></span>
    </div>
    <div class="col-md-6 fileupload-loadingbar">
        <!-- The global progress state -->
        <div class="fileupload-progress fade">
            <!-- The global progress bar -->
            <div class="progress active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width:0;"></div>
            </div>
            <!-- The extended global progress state -->
<!--            <div class="progress-extended">&nbsp;</div>-->
        </div>
    </div>
</div>
<!-- The table listing the files available for upload/download -->
<ul role="presentation" class="scroll-slider file-upload-presentation files"></ul>

<?= Html::endTag('div'); ?>
