<?php declare(strict_types=1);

namespace Frodo;

use Frodo\Exception\NotFoundException;
use Frodo\Validator\ShortUrl;
use Frodo\Database;

class Stats
{

    const LEADERBOARD_SIZE = 5;

    /** @var Database */
    private $db;

    public function __construct(string $datafile)
    {

        $this->db = new Database($datafile, new Encoder());
    }

    public function getGlobalStats(): array
    {

        return [
            'most_visited' => $this->db->getMostVisitedShortUrls(self::LEADERBOARD_SIZE),
            'most_shortened' => $this->db->getMostShortenedUrls(self::LEADERBOARD_SIZE),
        ];
    }

    public function getStats(string $short_url): array
    {
        (new ShortUrl($short_url))->validate();

        $res = $this->db->findByShortUrl($short_url);

        $id = $res['id'] ?? null;
        if (is_null($id)) {
            throw new NotFoundException();
        }

        $hits_per_day = $this->db->getVisitsPerDayForUrl($res['id']);
        $total_hits = $this->db->getTotalVisitsForUrl($res['id']);

        $create_date = date("c", $res['create_date']); // ISO 8601

        return [
            'short_url' => $short_url,
            'long_url' => htmlspecialchars($res['long_url']),
            'create_date' => $create_date,
            'total_hits' => $total_hits,
            'hits_per_day_hist' => self::histogram($hits_per_day),
        ];
    }

    public static function histogram(array $data, int $num_bins = 10): array
    {
        if ($num_bins == 0) {
            throw new \RuntimeException("histogram must have at least 1 bin");
        }

        $vals = array_values($data);
        $min = min($vals);
        $max = max($vals);

        $bins = [];
        $b = $max;
        while (count($bins) < $num_bins) {
            $b -= ($max - $min) / $num_bins;
            array_unshift($bins, $b);
        }

        $hist = array_combine(
            array_map(function ($bin) {
                return sprintf("%.2f", $bin);
            }, $bins),
            array_fill(0, count($bins), 0)
        );
        foreach ($vals as $val) {
            $i = 0;
            while ($i < count($bins) && $val > $bins[$i]) {
                $i++;
            }
            if ($i === count($bins)) {
                $i--;
            }
            $label = sprintf("%.2f", $bins[$i]);
            $hist[$label]++;
        }

        return $hist;
    }
}
