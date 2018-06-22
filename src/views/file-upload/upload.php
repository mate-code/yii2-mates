<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-upload slider-item">
        <span class="slider-item-actions action-set-collection">
            {% if (!i) { %}
                <span class="glyphicon glyphicon-remove cancel"></span>
            {% } %}
        </span>

        <div class="preview">
            <div class="upload-overlay start">
                <span class="glyphicon glyphicon-upload"></span>
            </div>
        </div>

        <div class="error text-danger"></div>

        <div class="upload-progress">
            <div class="progress active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar" style="width:0%;">
                </div>
                <div class="size"></div>
            </div>
        </div>
    </li>
{% } %}
</script>