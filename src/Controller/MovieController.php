<?php

namespace WeMovies\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tmdb\Model\Common\Video\Youtube;
use WeMovies\Provider\MovieProvider;

class MovieController extends AbstractController
{
    /**
     * @Route("/", name="wemovies_movie_home")
     * @param MovieProvider $movieProvider
     * @return Response
     */
    public function home(Request $request,MovieProvider $movieProvider): Response
    {
        $selectedGenre = $request->get('genre');
        $genres = $movieProvider->findGenre();

        $popularMovies = empty($selectedGenre) ? $movieProvider->findTopMovies() : $movieProvider->findTopMoviesByGenre($selectedGenre);
        $topMovie = $popularMovies->getIterator()->current();
        $topMovieTrailers = $topMovie->getVideos()->filter(function ($key, $video) {
            return $video instanceof Youtube && $video->getType() === 'Trailer';
        });
        /** @var Youtube $topMovieTrailer */
        $topMovieTrailer = $topMovieTrailers->getIterator()->current();
        $popularMovies->remove($popularMovies->getIterator()->key());

        return $this->render('\Movie\home.html.twig', [
            'selectedGenre' => $selectedGenre,
            'genres' => $genres,
            'topMovie' => $topMovie,
            'topMovieTrailer' => $topMovieTrailer->getKey(),
            'movies' => $popularMovies,
        ]);
    }

    /**
     * @Route("/movie/{id}")
     * @param int           $id
     * @param MovieProvider $movieProvider
     * @return Response
     */
    public function details(int $id, MovieProvider $movieProvider): Response
    {
        $movie = $movieProvider->loadMovie($id);
        $movieTrailers = $movie->getVideos()->filter(function ($key, $video) {
            return $video instanceof Youtube && $video->getType() === 'Trailer';
        });
        $movieTrailer = $movieTrailers->getIterator()->current();

        return $this->render('\Movie\_hero.html.twig', [
            'topMovie' => $movie,
            'topMovieTrailer' => $movieTrailer->getKey(),
        ]);
    }

    /**
     * @Route("/search/result")
     * @param Request $request
     * @return void
     */
    public function search(Request $request, MovieProvider $movieProvider): Response
    {
        $genres = $movieProvider->findGenre();
        $movies = $movieProvider->search($request->query->get('query'))->map(fn ($key, $movie, $context) => $movieProvider->loadMovie($movie->getId()));


        return $this->render('\Movie\home.html.twig', [
            'genres' => $genres,
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/search/autocomplete")
     * @param Request $request
     * @return void
     */
    public function searchAutocomplete(Request $request, MovieProvider $movieProvider): Response
    {
        $searchResult = $movieProvider->search($request->query->get('q'));

        return new JsonResponse(array_values(array_map(
            fn ($movie) => $movie->getTitle(),
            $searchResult->toArray()
        )));
    }
}