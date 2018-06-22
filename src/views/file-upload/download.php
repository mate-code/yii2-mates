<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-download slider-item">
        <span class="slider-item-actions action-set-collection">
            {% if (file.deleteUrl) { %}
                <span class="glyphicon glyphicon-trash delete"
                      data-type="{%=file.deleteType%}"
                      data-url="{%=file.deleteUrl%}"
                      {% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                </span>
            {% } else { %}
                <span class="glyphicon glyphicon-remove cancel"></span>
            {% } %}
        </span>

        <div class="preview" style="background-image: url({%=file.thumbnailUrl%})">
        <div class="loading"><div></div></div>
        </div>

        {% if (file.error) { %}
            <div class="error text-danger">{%=file.error%}</div>
        {% } %}
    </li>

{% } %}

</script>
