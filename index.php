<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog de Noticias</title>
    <!-- Enlace a Bootstrap para estilos predefinidos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace a archivo CSS personalizado -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">Noticias Recientes</h1>

    <!-- Indicador de carga (se muestra mientras se obtienen los datos) -->
    <div id="loading" class="text-center mt-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2">Cargando noticias...</p>
    </div>

    <!-- Contenedor donde se mostrarán las noticias -->
    <div id="news-container" class="row"></div>

    <!-- Sección de paginación -->
    <nav>
        <ul class="pagination justify-content-center mt-4">
            <li class="page-item disabled" id="prevPage">
                <a class="page-link" href="#">Anterior</a>
            </li>
            <li class="page-item disabled">
                <span class="page-link" id="currentPage">Página 1</span>
            </li>
            <li class="page-item" id="nextPage">
                <a class="page-link" href="#">Siguiente</a>
            </li>
        </ul>
    </nav>
</div>

<script>
const apiKey = "d8368367d8764af9962591bba513a728"; // Clave de acceso para NewsAPI
let page = 1;  // Página actual
const pageSize = 10; // Número de noticias por página

// Función para obtener las noticias de la API
async function fetchNews() {
    try {
        document.getElementById("loading").style.display = "block"; // Muestra el indicador de carga

        // Se hacen dos peticiones en paralelo (una para noticias y otra para autores aleatorios)
        const [newsResponse, usersResponse] = await Promise.all([
            fetch(`https://newsapi.org/v2/top-headlines?country=us&pageSize=${pageSize}&page=${page}&apiKey=${apiKey}`),
            fetch(`https://randomuser.me/api/?results=${pageSize}`)
        ]);

        // Se convierten las respuestas en formato JSON
        const newsData = await newsResponse.json();
        const usersData = await usersResponse.json();

        // Verifica si la respuesta es válida
        if (newsData.status !== "ok" || !usersData.results) {
            throw new Error("Error al obtener datos de las APIs.");
        }

        // Llama a la función para mostrar las noticias
        renderNews(newsData.articles, usersData.results);
    } catch (error) {
        document.getElementById("news-container").innerHTML = `<p class="text-center text-danger">${error.message}</p>`;
    } finally {
        document.getElementById("loading").style.display = "none"; // Oculta el indicador de carga
    }
}

// Función para renderizar (mostrar) las noticias en la página
function renderNews(articles, users) {
    let html = "";
    articles.forEach((article, index) => {
        const author = users[index]; // Se asigna un autor aleatorio a cada noticia
        html += `
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <img src="${article.urlToImage || 'https://via.placeholder.com/400'}" class="card-img-top" alt="Imagen no disponible">
                    <div class="card-body">
                        <h5 class="card-title">${article.title}</h5>
                        <p class="card-text">${article.description || "Descripción no disponible"}</p>
                        <p class="card-text"><strong>Autor:</strong> ${author.name.first} ${author.name.last}</p>
                        <a href="${article.url}" class="btn btn-primary" target="_blank">Leer más</a>
                    </div>
                </div>
            </div>`;
    });

    document.getElementById("news-container").innerHTML = html; // Inserta el HTML generado
    updatePagination(); // Actualiza los controles de paginación
}

// Función para actualizar los botones de paginación
function updatePagination() {
    document.getElementById("currentPage").textContent = `Página ${page}`;
    document.getElementById("prevPage").classList.toggle("disabled", page === 1);
}

// Eventos para cambiar de página
document.getElementById("prevPage").addEventListener("click", (e) => {
    e.preventDefault();
    if (page > 1) {
        page--;
        fetchNews();
    }
});

document.getElementById("nextPage").addEventListener("click", (e) => {
    e.preventDefault();
    page++;
    fetchNews();
});

// Cargar noticias al inicio
fetchNews();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
