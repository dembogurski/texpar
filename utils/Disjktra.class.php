<?php

/**
 * Algoritmo de  Disjktra para hallar el camino mas corto
 * En los pedidos de Mercaderias 
 * @author Ing.Douglas
 * @date 06/06/2017
 */
class Disjktra {

    function dijkstra($graph_array, $source, $target) {
        $vertices = array();
        $neighbours = array();
        foreach ($graph_array as $edge) {
            array_push($vertices, $edge[0], $edge[1]);
            $neighbours[$edge[0]][] = array("end" => $edge[1], "cost" => $edge[2]);
            $neighbours[$edge[1]][] = array("end" => $edge[0], "cost" => $edge[2]);
        }
        $vertices = array_unique($vertices);

        foreach ($vertices as $vertex) {
            $dist[$vertex] = INF;
            $previous[$vertex] = NULL;
        }

        $dist[$source] = 0;
        $Q = $vertices;
        while (count($Q) > 0) {

            // TODO - Find faster way to get minimum
            $min = INF;
            foreach ($Q as $vertex) {
                if ($dist[$vertex] < $min) {
                    $min = $dist[$vertex];
                    $u = $vertex;
                }
            }

            $Q = array_diff($Q, array($u));
            if ($dist[$u] == INF or $u == $target) {
                break;
            }

            if (isset($neighbours[$u])) {
                foreach ($neighbours[$u] as $arr) {
                    $alt = $dist[$u] + $arr["cost"];
                    if ($alt < $dist[$arr["end"]]) {
                        $dist[$arr["end"]] = $alt;
                        $previous[$arr["end"]] = $u;
                    }
                }
            }
        }
        $path = array();
        $u = $target;
        while (isset($previous[$u])) {
            array_unshift($path, $u);
            $u = $previous[$u];
        }
        array_unshift($path, $u);
        return $path;
    }

}



?>

