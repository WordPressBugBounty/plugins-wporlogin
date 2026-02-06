<?php

/**
 * Registra todas las acciones y filtros para el plugin.
 *
 * Mantiene una lista de todos los hooks que deben registrarse 
 * a través de la API de plugins de WordPress.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Loader {

    /**
     * El array de acciones registradas con WordPress.
     *
     * @access protected
     * @var    array    $actions    Las acciones registradas con WordPress para disparar cuando el plugin cargue.
     */
    protected $actions;

    /**
     * El array de filtros registrados con WordPress.
     *
     * @access protected
     * @var    array    $filters    Los filtros registrados con WordPress para disparar cuando el plugin cargue.
     */
    protected $filters;

    /**
     * Inicializa las colecciones utilizadas para mantener las acciones y filtros.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    /**
     * Añade una nueva acción a la colección para ser registrada con WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             El nombre de la acción de WordPress a la que se está enganchando.
     * @param    object               $component        Una referencia a la instancia del objeto en el que está definida la devolución de llamada.
     * @param    string               $callback         El nombre de la definición de la función en el $componente.
     * @param    int                  $priority         Opcional. La prioridad en la que se debe ejecutar la función callback. El valor predeterminado es 10.
     * @param    int                  $accepted_args    Opcional. El número de argumentos que deben pasarse a la función callback. El valor predeterminado es 1.
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Añade un nuevo filtro a la colección para ser registrado con WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             El nombre del filtro de WordPress al que se está enganchando.
     * @param    object               $component        Una referencia a la instancia del objeto en el que está definida la devolución de llamada.
     * @param    string               $callback         El nombre de la definición de la función en el $componente.
     * @param    int                  $priority         Opcional. La prioridad en la que se debe ejecutar la función callback. El valor predeterminado es 10.
     * @param    int                  $accepted_args    Opcional. El número de argumentos que deben pasarse a la función callback. El valor predeterminado es 1.
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Una función de utilidad que se utiliza para registrar las acciones y los ganchos en una sola colección.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            La colección de ganchos que se está registrando (acciones o filtros).
     * @param    string               $hook             El nombre del filtro de WordPress al que se está enganchando.
     * @param    object               $component        Una referencia a la instancia del objeto en el que está definida la devolución de llamada.
     * @param    string               $callback         El nombre de la definición de la función en el $componente.
     * @param    int                  $priority         La prioridad en la que se debe ejecutar la función callback.
     * @param    int                  $accepted_args    El número de argumentos que deben pasarse a la función callback.
     * @return   array                                  La colección de ganchos registrados en WordPress para iterar.
     */
    private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Registra los filtros y las acciones con WordPress.
     *
     * @since    1.0.0
     */
    public function run() {

        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

    }

}