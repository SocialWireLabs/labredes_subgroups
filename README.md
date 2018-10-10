# labredes_subgroups
Elgg plugin for subgroups support in group contexts in social learning environments (Elgg 1.12)
## Autores: M. Rodríguez-Pérez, S. Herrería-Alonso
### Required plugins (dependences): 
- group_tools
- labredes_html5views
- groups

1.- Objetivo
	El plugin proporciona la funcionalidad de administrador subgrupos dentro de un grupo.

2.- Funcionalidad para el usuario
	Los administradores de grupo (dueño y operadores) pueden:
	
	a) Añadir subgrupos. Los subgrupos pueden ser:
		i) Abiertos: en cuyo caso cualquier miembro del grupo puede apuntarse o borrarse
		ii) Cerrados: solo los administradores podrán gestionar los miembros del grupo
		
		Además, los subgrupos tienen un cupo máximo de miembros que no puede ser sobrepasado.
		
	b) Añadir y eliminar miembros del grupo a los subgrupos.
	c) Ver la lista de miembros actuales de un subgrupo.
	
	Los miembros del grupo al que pertenecen los subgrupos pueden:
	
	a) Ver la lista de subgrupos.
	b) Si el subgrupo es abierto, apuntarse o borrarse de algún subgrupo.
	c) Si el subgrupo es abierto, o pertenecen a él: consultar la lista de miembros del subgrpuo.
	
	Los usuarios pueden pertenecer como máximo a un subgrupo.
	
3.- Funcionalidad para el programador
	Los subgroups se implementan como un subtipo de ElggGroup, por tanto, toda la funcionalidad del
API de grupos está directamente disponible:
	
	a) Comprobar si un usuario es miembro
	b) Listado de miembros
	c) Gestión de permisos
	d) ...
	
	A mayores, existen una serie de funciones de utilidad definidas por el plugin. La mayoría de ellas
	han sido creadas para consumo interno, pero pueden ser reutilizadas sin problema:
	
	a) lbr_subgroups_gatekeeper: adapta group_gatekeeper para vistas que deban ser restringidas a miembros
	de subgrupos. La principal diferencia es que permite el acceso de los administradores del grupo contenedor.
	b) lbr_subgroups_is_group_admin: comprueba si un usuario es administrador del grupo contenedor
	c) lbr_subgroups_is_allowed_to_join: comprueba si un usuario puede añadirse a un subgrupo
	d) lbr_subgroups_is_allowed_to_leave: comprueba si un usuario puede abandonar un subgrupo
	e) lbr_subgroups_count_membership: devuelve el número de subgrupos del actual grupo al que pertenece un usuario.
		Como máximo debería valer 1.
