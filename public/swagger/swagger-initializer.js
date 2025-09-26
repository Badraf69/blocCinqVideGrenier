window.onload = function() {
    window.ui = SwaggerUIBundle({
        url: "./openapi.yaml", // notre spec locale
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        layout: "BaseLayout"
    });
};
