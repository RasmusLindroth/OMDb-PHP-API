# OMDb-PHP-API
A wrapper for OMDb API that gets movie info by IMDb id or title and returns data from IMDb and Rotten Tomatoes.

### How to use
    //Init OMDb and include data from Rotten Tomatoes
    $omdb = new OMDb( ['tomatoes' => TRUE] );

    //Get by title
    $movie = $omdb->get_by_title('Pulp Fiction');

    //Get by IMDb id
    $movie = $omdb->get_by_id('tt0057012');

### Parameters for the constructor
    $omdb = new OMDb($params = [], $timeout = 5, $date_format = 'Y-m-d');

Parameters
| Parameter |     Valid Options      | Default value |       Description       |
|-----------|------------------------|---------------|-------------------------|
| type      | movie, series, episode | NULL          | Type of result          |
| y         |                        | NULL          | Year of release         |
| plot      | short, full            | short         | Plot-length             |
| tomatoes  | TRUE, FALSE            | FALSE         | Include Rotten Tomatoes |


Timeout = timeoutvalue for cURL

Date_format = http://php.net/manual/function.date.php
and NULL for UNIX time

### Output example
    array (size=34)
      'Title' => string 'Dr. Strangelove or: How I Learned to Stop Worrying and Love the Bomb'
      'Year' => string '1964'
      'Rated' => string 'PG'
      'Released' => string '1964-01-29'
      'Runtime' => int 95
      'Genre' =>
        array (size=2)
          0 => string 'Comedy'
          1 => string 'War'
      'Director' => string 'Stanley Kubrick'
      'Writer' =>
        array (size=4)
          0 => string 'Stanley Kubrick (screenplay)'
          1 => string 'Terry Southern (screenplay)'
          2 => string 'Peter George (screenplay)'
          3 => string 'Peter George (based on the book: "Red Alert" by)'
      'Actors' =>
        array (size=4)
          0 => string 'Peter Sellers'
          1 => string 'George C. Scott'
          2 => string 'Sterling Hayden'
          3 => string 'Keenan Wynn'
      'Plot' => string 'An insane general triggers a path to nuclear holocaust that a war room full of politicians and generals frantically try to stop.'
      'Language' =>
        array (size=2)
          0 => string 'English'
          1 => string 'Russian'
      'Country' =>
        array (size=2)
          0 => string 'USA'
          1 => string 'UK'
      'Awards' => string 'Nominated for 4 Oscars. Another 15 wins & 4 nominations.'
      'Poster' => string 'http://ia.media-imdb.com/images/M/MV5BMTU2ODM2NTkxNF5BMl5BanBnXkFtZTcwOTMwMzU3Mg@@._V1_SX300.jpg'
      'Metascore' => int 96
      'imdbRating' => float 8.5
      'imdbVotes' => int 291737
      'imdbID' => string 'tt0057012'
      'Type' => string 'movie'
      'tomatoMeter' => int 99
      'tomatoImage' => string 'certified'
      'tomatoRating' => float 9
      'tomatoReviews' => int 68
      'tomatoFresh' => int 67
      'tomatoRotten' => int 1
      'tomatoConsensus' => string 'Stanley Kubrick's brilliant Cold War satire remains as funny and razor-sharp today as it was in 1964.'
      'tomatoUserMeter' => int 94
      'tomatoUserRating' => float 4.2
      'tomatoUserReviews' => int 204995
      'DVD' => string '02 Nov 2004'
      'BoxOffice' => null
      'Production' => string 'Sony Pictures'
      'Website' => null
      'Response' => boolean true

OMDb APIs webpage and a thanks to Brian Fritz
http://www.omdbapi.com/

Made by:
Rasmus Lindroth