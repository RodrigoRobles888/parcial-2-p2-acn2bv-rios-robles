<?php
require_once("./components/styleSetup.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Concesionaria R|R</title>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body class="ppal">

	<?php require("./components/header.php"); ?>

	<div class="container mt-3 text-center">
		<form id="filterForm" class="d-flex justify-content-center gap-2 mb-3">
			<input id="nombreInput" type="text" name="nombre" placeholder="Buscar Marca...">
			<select id="categoriaSelect" name="categoria">
				<option value="">Todas las categorías</option>
				<option value="Alta Gama">Alta Gama</option>
				<option value="Media Gama">Media Gama</option>
			</select>
			<button class="btn btn-primary" type="submit">Filtrar</button>
		</form>

		<div>
			<button id="temaBtn" class="btn btn-primary mb-3">Cambiar Tema</button>
		</div>

		<div id="cardsContainer" class="row justify-content-center"></div>

		<div id="msg"></div>

		<div class="conteiner2">
			<h4>Agregar nuevo ítem</h4>
			<div class="formulario">
				<form id="addForm">
					<input name="name" type="text" placeholder="Nombre" required class="form-control mb-2">
					<input name="categoria" type="text" placeholder="Categoria" required class="form-control mb-2">
					<input name="descrip" type="text" placeholder="Descripcion" required class="form-control mb-2">
					<input name="url" type="text" placeholder="URL imagen (opcional)" class="form-control mb-2">
					<input name="link" type="text" placeholder="Link (opcional)" class="form-control mb-2">
					<button class="btn btn-primary" type="submit">Agregar</button>
				</form>
			</div>
		</div>
	</div>

	<?php require("./components/footer.php"); ?>

	<script>
		const container = document.getElementById('cardsContainer');
		const msg = document.getElementById('msg');

		async function loadItems(params = {}) {
			const url = new URL(window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/') + 'api.php');
			Object.keys(params).forEach(k => {
				if (params[k]) url.searchParams.append(k, params[k]);
			});
			try {
				const res = await fetch(url.toString());
				const data = await res.json();
				renderCards(data.items || []);
			} catch (e) {
				console.error(e);
			}
		}

		function renderCards(items) {
			container.innerHTML = '';
			if (items.length === 0) {
				container.innerHTML = '<p class="text-center">No se encontraron resultados.</p>';
				return;
			}
			items.forEach(it => {
				const col = document.createElement('div');
				col.className = 'card m-3';
				col.style.width = '18rem';
				col.innerHTML = `
            <img src="${it.url}" class="card-img-top" alt="${it.name}">
            <div class="card-body">
                <h5 class="card-title">${it.name}</h5>
                <h6 class="categoria">${it.categoria}</h6>
                <p class="card-text">${it.descrip}</p>
                <a href="${it.link}" class="btn btn-primary" target="_blank">Ver Modelos</a>
            </div>
        `;
				container.appendChild(col);
			});
		}

		document.getElementById('filterForm').addEventListener('submit', function(e) {
			e.preventDefault();
			const nombre = document.getElementById('nombreInput').value.trim();
			const categoria = document.getElementById('categoriaSelect').value;
			loadItems({
				nombre,
				categoria
			});
		});

		loadItems();

		document.getElementById('addForm').addEventListener('submit', async function(e) {
			e.preventDefault();
			const form = e.target;
			const formData = new FormData(form);
			const payload = {};
			formData.forEach((v, k) => payload[k] = v);

			if (!payload.name || !payload.categoria || !payload.descrip) {
				Swal.fire({
					icon: 'error',
					title: 'Faltan campos obligatorios'
				});
				return;
			}

			try {
				const res = await fetch('api.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(payload)
				});
				const data = await res.json();
				if (data.success) {
					msg.innerHTML = '<p class="alert alert-success">Ítem agregado.</p>';
					loadItems();
					form.reset();
				} else {
					msg.innerHTML = `<p class="alert alert-danger">${data.message || 'Error'}</p>`;
				}
			} catch (err) {
				console.error(err);
				msg.innerHTML = '<p class="alert alert-danger">Error de red.</p>';
			}
		});

		document.getElementById('temaBtn').addEventListener('click', function() {
			const url = new URL(window.location.href);
			const tema = url.searchParams.get('tema') === 'oscuro' ? 'claro' : 'oscuro';
			url.searchParams.set('tema', tema);
			window.location.href = url.toString();
		});
	</script>

</body>

</html>