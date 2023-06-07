<?php

declare(strict_types=1);

namespace WeMovies\Provider;

use Tmdb\Client;
use Tmdb\Model\AbstractModel;
use Tmdb\Model\Collection\Genres;
use Tmdb\Model\Collection\ResultCollection;
use Tmdb\Model\Movie;
use Tmdb\Model\Search\SearchQuery\MovieSearchQuery;
use Tmdb\Repository\GenreRepository;
use Tmdb\Repository\MovieRepository;
use Tmdb\Repository\SearchRepository;

class MovieProvider
{
    private MovieRepository $movieRepository;
    private GenreRepository $genreRepository;
    private SearchRepository $searchRepository;

    public function __construct(
        SearchRepository $searchRepository,
        MovieRepository $movieRepository,
        GenreRepository $genreRepository
    ) {

        $this->searchRepository = $searchRepository;
        $this->movieRepository = $movieRepository;
        $this->genreRepository = $genreRepository;
    }

    public function search(string $query): ResultCollection
    {
        $result = $this->searchRepository->searchMovie($query, new MovieSearchQuery());

        return $result;
    }

    public function findTopMovies(): ResultCollection
    {
        $movies = $this->movieRepository->getPopular()->map(fn ($key, $movie, $context) => $this->loadMovie($movie->getId()));

        return $movies;
    }

    public function findGenre(): Genres
    {
        $genres = $this->genreRepository->loadMovieCollection();

        return $genres;
    }

    public function findTopMoviesByGenre(int $genreId): ResultCollection
    {
        $movies = $this->genreRepository->getMovies($genreId)->map(fn ($key, $movie, $context) => $this->loadMovie($movie->getId()));

        return $movies;
    }

    /**
     * @param int $movie
     * @return AbstractModel|Movie
     */
    public function loadMovie(int $movie): AbstractModel
    {
        return $this->movieRepository->load($movie);
    }
}