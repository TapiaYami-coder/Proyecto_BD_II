</main>
</div>
<footer class="pie">OBRA CONTROL - Sistema de gestión de construcciones</footer>
<script>
    (function () {
        function iniciarTabla(modulo) {
            const tabla = modulo.querySelector('.tabla-datos'); if (!tabla) return;
            tabla.dataset.pagina = '1'; tabla.dataset.orden = ''; tabla.dataset.direccion = '1';
            const tbody = tabla.querySelector('tbody'); const filas = Array.from(tbody.rows); const porPagina = 8;
            const buscador = modulo.querySelector('.campo-busqueda-tabla');
            const indicador = modulo.querySelector('.indicador-pagina');
            function pintar() {
                const activas = filas.filter(f => f.style.display !== 'none');
                const total = Math.max(1, Math.ceil(activas.length / porPagina));
                const actual = Math.min(parseInt(tabla.dataset.pagina, 10) || 1, total);
                tabla.dataset.pagina = actual;
                filas.forEach(f => f.style.display = 'none');
                activas.slice((actual - 1) * porPagina, actual * porPagina).forEach(f => f.style.display = '');
                indicador.textContent = activas.length ? 'Página ' + actual + ' de ' + total : 'Sin resultados';
            }
            buscador.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                filas.forEach(f => f.style.display = f.textContent.toLowerCase().includes(q) ? '' : 'none');
                tabla.dataset.pagina = '1'; pintar();
            });
            modulo.querySelectorAll('.cabecera-ordenable').forEach(th => {
                const ordenar = () => {
                    const i = +th.dataset.columna;
                    const dir = tabla.dataset.orden === String(i) && tabla.dataset.direccion === '1' ? -1 : 1;
                    tabla.dataset.orden = String(i); tabla.dataset.direccion = String(dir);
                    filas.sort((a, b) => {
                        const x = a.cells[i].textContent.trim(), y = b.cells[i].textContent.trim();
                        const nx = parseFloat(x.replace(/,/g, '')), ny = parseFloat(y.replace(/,/g, ''));
                        const resultado = !isNaN(nx) && !isNaN(ny) ? nx - ny : x.localeCompare(y, 'es');
                        return resultado * dir;
                    });
                    filas.forEach(f => tbody.appendChild(f));
                    modulo.querySelectorAll('.icono-orden').forEach(s => s.textContent = '↕');
                    th.querySelector('.icono-orden').textContent = dir === 1 ? '▲' : '▼';
                    pintar();
                };
                th.addEventListener('click', ordenar);
                th.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); ordenar(); } });
            });
            modulo.querySelectorAll('.boton-pagina').forEach(b => b.addEventListener('click', () => {
                tabla.dataset.pagina = String((parseInt(tabla.dataset.pagina, 10) || 1) + (+b.dataset.direccion)); pintar();
            }));
            pintar();
        }
        window.exportarTabla = function (boton) {
            const modulo = boton.closest('.modulo-tabla'); const tabla = modulo.querySelector('.tabla-datos');
            const filas = Array.from(tabla.querySelectorAll('tbody tr'));
            const columnas = Array.from(tabla.querySelectorAll('thead th')).map(th => th.textContent.replace(/[↕▲▼]/g, '').trim());
            const esc = v => '"' + v.replace(/"/g, '""') + '"';
            const lineas = [columnas.map(esc).join(',')].concat(filas.map(f => Array.from(f.cells).map(c => esc(c.textContent.trim())).join(',')));
            const blob = new Blob(['\ufeff' + lineas.join('\n')], { type: 'text/csv;charset=utf-8' });
            const enlace = document.createElement('a'); enlace.href = URL.createObjectURL(blob); enlace.download = 'obra_control.csv'; enlace.click(); URL.revokeObjectURL(enlace.href);
        };
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.modulo-tabla').forEach(iniciarTabla);
            const actual = location.pathname.split('/').pop();
            document.querySelectorAll('.enlace-menu').forEach(a => { if (a.getAttribute('href') === actual) a.classList.add('activo'); });
        });
    })();
</script>