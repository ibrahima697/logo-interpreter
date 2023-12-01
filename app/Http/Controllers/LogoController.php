<?php

namespace App\Http\Controllers;

use App\Models\LogoCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class LogoController extends Controller
{
    protected $currentPosition;
    protected $isVisible = true;
    protected $isHidden = true;
    protected $isDrawing = true;
    protected $positionX;
    protected $positionY;
    protected $angle;
    protected $color;
    protected $distance;

    public function index(Request $request)
    {
     
        try {
            if ($request->isMethod('post')) {
                $command = $request->input('command');
                $currentPosition = $this->getInitialTurtlePosition();
    
                // Interpréter la commande avec la bibliothèque ML
                $interpretationResult = $this->interpretWithMLLibrary($command, $currentPosition);
    
                // Retourner les données interprétées au format JSON
                return response()->json($interpretationResult);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur dans LogoController : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    public function getCurrentAngle()
    {
        return $this->angle;
    }

    public function setAbsolutePosition($x, $y)
    {
        // Définir les nouvelles coordonnées absolues par rapport au centre
        $this->positionX = $x;
        $this->positionY = $y;
    
        // Retourner la position actuelle après la modification
        return [
            'x' => $this->positionX,
            'y' => $this->positionY,
            'angle' => $this->angle, // Assurez-vous que vous avez la propriété d'angle dans votre contrôleur
            // ... autres propriétés de position ...
        ];
    }
    private function interpretWithMLLibrary($commands, $currentPosition)
{
        $parts = explode(' ', $commands);

        // Obtenir la première partie (le nom de la commande)
        $action = strtoupper($parts[0]);
        // ... logique d'exécution ...
        $newPosition = $currentPosition;

    // Logique d'interprétation avec la bibliothèque ML
    $commands = [
         "AV" => "Avancer" ,
         "RE" => "Reculer",
         "TD" => "Tourner à Droite",
         "TG" => "Tourner à Gauche",
         "LC" => "La tortue ne laisse pas de trace",
         "BC" => "La tortue laisse une trace",
         "CT" => "La tortue est cachée",
         "MC" => "La tortue est visible",
         "VE" => "Efface l'ecran et replace la tortue au centre",
         "NETTOIE" => "Efface l'ecran sans changer la position de la tortue",
         "ORIGINE" => "Replace la tortue au centre",
         "VT" => "Efface la console",
         "FCC" => "Change la couleur du trait",
         "FCB" => "Change la couleur du fond",
         "FCAP" => "Fixe l'angle de la tortue",
         "CAP" => "Retourne l'angle de la tortue",
         "FPOS" => "Position de la tortue x,y",
         "POS" => "Retourne la position",
         'REPETE' => 'Répéter',
    ];
    if (array_key_exists($action, $commands)) {
    switch ($action) {
        case 'AV':
            // Logique pour avancer
            $distance = isset($parts[1]) ? intval($parts[1]) : 1; // Par défaut, avancer de 1 unité
            $newPosition = $this->moveForward($newPosition, $distance, $currentPosition['angle']);
            break;

        case 'RE':
            // Logique pour reculer
            $distance = isset($parts[1]) ? intval($parts[1]) :1;
            $newPosition = $this->moveBack($newPosition, $distance, $currentPosition['angle']);
            break;
        case 'TD':
            $distance = isset($parts[1]) ? intval($parts[1]) :1;
            $newPosition = $this->turnRight($newPosition,$currentPosition['angle']);
            break;
        case 'TG':
            $distance = isset($parts[1]) ? intval($parts[1]) :1;
            $newPosition = $this->turnLeft($newPosition, $currentPosition['angle']);
            break;
        case 'LC':
            $distance = isset($parts[1]) ? intval($parts[1]) :1;
            $newPosition = $this->noTrack($newPosition, $currentPosition['angle']);
            break;
        case 'BC':
            $distance = isset($parts[1]) ? intval($parts[1]) :1;
            $newPosition = $this->trackBack($newPosition, $currentPosition['angle']);
            break;
        case 'CT':
            $newPosition = $this->hideTurtle($newPosition, $currentPosition['angle']);
            break;
        case 'MC':
            $newPosition = $this->showTurtle($newPosition, $currentPosition['angle']);
            break;
        case 'VE':
            $newPosition = $this->clearScreenAndMoveToCenter();

            break;
        case 'NETTOIE':
            $newPosition = $this->clearScreenWithoutMoving($newPosition, $currentPosition['angle']);
            break;
        case 'VT':
            $newPosition = $this->clearConsole($currentPosition);
        break;
        case 'ORIGINE':
            $newPosition = $this->moveToCenter($newPosition,$canvasWidth, $canvasHeight);
            break;
        case 'FCB':
            if (count($parts) >= 2) {
                $color = $parts[1]; // Récupérez la couleur spécifiée dans la commande
                $newPosition = $this->changeBackgroundColor($color, $currentPosition['backgroundcolor']);
            } else {
                // Gérez le cas où la commande ne contient pas suffisamment d'arguments
                return response()->json(['error' => 'La commande FCB nécessite une couleur en argument'], 400);
            }
            break;
        case 'FCC':
            $color = $parts[1]; // Récupérer la couleur spécifiée dans la commande
            $newPosition = $this->setPenColor($color, $currentPosition);
            break;
        case 'CAP':
            $angle =  $parts[1];
            $currentAngle = $this->getCurrentAngle();
            return $currentAngle;            
            break;
        case 'FCAP':    
                $angle = 45; // Récupérer l'angle spécifié dans la commande
                $newPosition = $this->setAbsoluteAngle($angle);
                 break;
        case 'FPOS':
            $x = $parts[1]; // Récupérer la coordonnée x spécifiée dans la commande
            $y = $parts[2]; // Récupérer la coordonnée y spécifiée dans la commande
            $newPosition = $this->setAbsolutePosition($x, $y);
            break;
        case 'POS':
                // Logique pour retourner la position de la tortue
                $currentPosition = [
                    'x' => $this->positionX,
                    'y' => $this->positionY,
                    'angle' => $this->angle,
                    // ... autres propriétés de position ...
                ];
            
                return $currentPosition;
        break;
        case 'REPETE':
            // Logique pour répéter un bloc d'instructions
            $repeatCount = isset($parts[1]) ? intval($parts[1]) : 1;
            $block = implode(' ', array_slice($parts, 3, -1)); // Obtenez le bloc d'instructions entre crochets
            $newPosition = $this->repeatBlock($block, $repeatCount, $currentPosition);
            break;

        default:
            // Cas par défaut pour les commandes non reconnues
            $newPosition = $currentPosition;  

        }
    } else { 
    // Retourner le résultat de l'interprétation
    return 'commande non reconnue';
    }
        return $newPosition;
  

    // Remplacer ceci par votre logique réelle
    
}
private function repeatBlock($block, $repeatCount, $currentPosition)
{
    $newPosition = $currentPosition;

    for ($i = 0; $i < $repeatCount; $i++) {
        // Interprétez chaque commande dans le bloc
        $commandsInBlock = explode(' ', $block);
        foreach ($commandsInBlock as $command) {
            $newPosition = $this->interpretSingleCommand($command, $newPosition);
        }
    }

    return $newPosition;
}

private function interpretSingleCommand($command, $currentPosition)
{
    // Logique pour interpréter une seule commande
    $command = strtoupper(trim($command));

    // Logique pour interpréter une seule commande
    switch ($command) {
        case 'AV':
            $newPosition = $this->moveForward($currentPosition, 1);
            break;
        case 'RE':
            $newPosition = $this->moveBack($currentPosition, 1);
            break;
        case 'TD':
            $newPosition = $this->turnRight($currentPosition,1);
            break;
        case 'TG':
            $newPosition = $this->turnLeft($currentPosition,1);
            break;
        

        default:
            // Cas par défaut pour les commandes non reconnues
            $newPosition = $currentPosition;
            break;
    }


    return $newPosition;
}
private function getInitialTurtlePosition()
{
    $canvasWidth = 150/* la largeur de votre canvas */;
    $canvasHeight = 100/* la hauteur de votre canvas */;
    // Logique pour obtenir la position initiale de la tortue
    $initialPosition = [
        'x' => $canvasWidth / 2, // Valeur initiale pour la coordonnée x
        'y' => $canvasHeight / 2, // Valeur initiale pour la coordonnée y
        'angle' => 90, // Valeur initiale pour l'angle
        // ... autres propriétés de position ...
    ];

    return $initialPosition;
}



private function moveForward($currentPosition, $distance, $angle)
{
    $currentPosition = $this->getInitialTurtlePosition();

    // Convertir l'angle en radians
    $angleInRadians = deg2rad($angle);

    // Calculer les nouvelles coordonnées en fonction de l'angle et de la distance
    $newPosition['x'] = $currentPosition['x'] + $distance * cos($angleInRadians);
    $newPosition['y'] = $currentPosition['y'] + $distance * sin($angleInRadians);

    // Mettre à jour l'angle si nécessaire
    $newPosition['angle'] = $angle;

    // ... autres mises à jour nécessaires ...

    return $newPosition;
}
        
private function moveBack($currentPosition, $distance, $angle)
{
    $currentPosition = $this->getInitialTurtlePosition();
    // Convertir l'angle en radians
    $angleInRadians = deg2rad($angle);

    // Calculer les nouvelles coordonnées en fonction de l'angle et de la distance
    $newPosition['x'] = $currentPosition['x'] - $distance * cos($angleInRadians);
    $newPosition['y'] = $currentPosition['y'] - $distance * sin($angleInRadians);

    // Mettre à jour l'angle si nécessaire
    $newPosition['angle'] = $angle;


    return $newPosition;
}
    private function turnRight($currentPosition, $angle)
    {
        $currentPosition = $this->getInitialTurtlePosition();

          // Ajouter un angle négatif pour tourner à gauche
    $newAngle = $currentPosition['angle'] + $angle;

    // S'assurer que l'angle reste dans la plage [0, 360)
    $newAngle = ($newAngle + 360) % 360;

    // Mettre à jour l'angle dans la nouvelle position
    $newPosition['angle'] = $newAngle;

    // copier les autres coordonnées sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];

    return $newPosition;
    }
    private function turnLeft($currentPosition, $angle)
    {
        $currentPosition = $this->getInitialTurtlePosition();
          // Ajoutez un angle négatif pour tourner à gauche
    $newAngle = $currentPosition['angle'] - $angle;

    // Assurez-vous que l'angle reste dans la plage [0, 360)
    $newAngle = ($newAngle + 360) % 360;

    // Mettez à jour l'angle dans la nouvelle position
    $newPosition['angle'] = $newAngle;

    // copier les autres coordonnées sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];

    return $newPosition;
    }
    private function noTrack($currentPosition)
{
    // Marquer la tortue pour ne pas laisser de trace
    $currentPosition = $this->getInitialTurtlePosition();
     // Vérifier si la clé 'isDrawing' existe dans $currentPosition
     if (array_key_exists('isDrawing', $currentPosition)) {
        // La clé 'isDrawing' existe, nous pouvons l'utiliser
        $newPosition['isDrawing'] = false;
    } else {
        // La clé 'isDrawing' n'existe pas, vous pouvez choisir un comportement par défaut ici
        // Par exemple, nous pourrions définir la clé 'isDrawing' à false
        $newPosition['isDrawing'] = false;
    }

    // copier les autres coordonnées sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['angle'] = $currentPosition['angle'];


    return $newPosition;
}

private function trackBack($currentPosition)
{
    $currentPosition = $this->getInitialTurtlePosition();
    // Marquer la tortue pour laisser une trace
    $newPosition['isDrawing'] = true;

    // copier les autres coordonnées sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['angle'] = $currentPosition['angle'];

    return $newPosition;
}

private function hideTurtle($currentPosition)
{
    if (!isset($currentPosition['isVisible'])) {
        $currentPosition['isVisible'] = false; // ou false, selon votre logique par défaut
    }

    // Marquer la tortue comme cachée
    $newPosition['isVisible'] = false;

    // Copier les autres coordonnées sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['angle'] = $currentPosition['angle'];

    return $newPosition;
}

private function showTurtle($currentPosition)
{
    // Marquer la tortue comme visible
    $newPosition['isVisible'] = true;

    // Copier les autres coordonnées sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['angle'] = $currentPosition['angle'];

    return $newPosition;
}

private function clearScreen($currentPosition)
{
    // Effacer l'écran en replaçant la tortue au centre
    $newPosition['x'] = 0;
    $newPosition['y'] = 0;
    $newPosition['angle'] = 0;
    $newPosition['isVisible'] = true;
    $newPosition['isDrawing'] = true;

    return $newPosition;
}
private function clearScreenWithoutMoving($currentPosition)
{
    // Effacer l'écran sans changer la position
    // Marquer la tortue pour laisser une trace (si nécessaire)
    $newPosition = $currentPosition;


    return $newPosition;
}
private function clearConsole()
{
    // Ajouter votre logique pour effacer la console ici
    // Retourner la position actuelle de la tortue ou d'autres données nécessaires
    return $currentPosition;
}
private function moveToCenter($newPosition, $canvasWidth, $canvasHeight)
{
   
    // Replace la tortue au centre
    $newPosition = $currentPosition;
    $newPosition['x'] = $canvasWidth / 2;
    $newPosition['y'] = $canvasHeight / 2;

    return $newPosition;
}
private function changeLineColor($color, $currentPosition)
{
    // Changer la couleur du trait
    $newPosition['lineColor'] = $color;

    // Copier les autres indicateurs sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['angle'] = $currentPosition['angle'];
    $newPosition['isDrawing'] = $currentPosition['isDrawing'];
    $newPosition['isVisible'] = $currentPosition['isVisible'];

    return $newPosition;
}

private function getBackgroundColor($color)
{
    // Vérifiez d'abord si la clé 'backgroundColor' existe dans le tableau
    if (array_key_exists('backgroundColor', $color)) {
        return $color['backgroundColor'];
    }

    // Par défaut, retournez une valeur par défaut ou lancez une exception selon vos besoins
    return '#RRGGBB'; // Valeur par défaut blanche, remplacez-la par ce que vous préférez
}

// Utilisation dans votre méthode changeBackgroundColor
private function changeBackgroundColor($color, $currentPosition)
{
    $backgroundColor = $this->getBackgroundColor($color);
 // Changer la couleur du fond
 if ($color) {
    $newPosition['backgroundColor'] = $color;
} else {
    // Si aucune couleur n'est spécifiée, conservez la couleur actuelle
}

// Copiez les autres indicateurs sans les modifier
$newPosition['x'] = $currentPosition['x'];
$newPosition['y'] = $currentPosition['y'];
$newPosition['angle'] = $currentPosition['angle'];
$newPosition['isDrawing'] = $currentPosition['isDrawing'];
$newPosition['isVisible'] = $currentPosition['isVisible'];

// Set la couleur de fond du canvas
return $newPosition;
}

private function clearScreenAndMoveToCenter()
{
    // Logique pour effacer l'écran
    $newPosition['x'] = 0;
    $newPosition['y'] = 0;
    $newPosition['angle'] = 0;
    $newPosition['isVisible'] = true;
    $newPosition['isDrawing'] = true;

    // Logique pour replacer la tortue au centre
    $newPosition = $this->moveToCenter($newPosition );

    return $newPosition;
}

private function setPenColor($color, $currentPosition)
{
    $newPosition['lineColor'] = $color;

    // Copier les autres indicateurs sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['angle'] = $currentPosition['angle'];
    $newPosition['isVisible'] = true;
    $newPosition['isDrawing'] = true;
    // Retourner la position actuelle de la tortue ou d'autres données nécessaires
    return $currentPosition;
}

private function setAbsoluteAngle($angle)
{
    // Implémenter la logique pour fixer l'angle de la tortue de façon absolue
    // S'assurer que l'angle est dans la plage [0, 360)
    $angle = ($angle + 360) % 360;

    // Mise à jour de l'angle dans la nouvelle position
    $newPosition['angle'] = $angle;

    // Copier les autres coordonnées sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['isVisible'] = true;
    $newPosition['isDrawing'] = true;
    // s'assurer de valider et formater correctement l'angle
    $newPosition['angle'] = $currentPosition['angle'];

    // ...

    // Retourner la position actuelle de la tortue ou d'autres données nécessaires
    return $currentPosition;
}

}
