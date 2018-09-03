<?php
include 'omdb.class.php';

try {
    //Init OMDb and include data from Rotten Tomatoes
    $omdb = new OMDb( ['tomatoes' => TRUE, 'apikey' => '00000000'] );

    //Get by title
    $movie = $omdb->get_by_title('Pulp Fiction');

    //Get by IMDb id
    $movie = $omdb->get_by_id('tt0057012');

    //Get episodes by IMDb id and season number (1)
    $movie = $omdb->get_by_id('tt2085059', 1);

    //Get episode 2 by IMDB id and season number (1)
    $movie = $omdb->get_by_id('tt2085059', 1, 2);

    //Get multiple titles, limited info see the README
    $movie = $omdb->search('James Bond');

    //Get multiple titles with pagination
    $movie = $omdb->search('James Bond', 2);

}catch(Exception $e) {
    echo $e->getMessage();
}
?>
