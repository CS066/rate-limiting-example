<?php


namespace Pushwoosh\RateLimiting;


class SlidingWindow implements RateLimitingInterface
{
    /**
     * @var int
     */
    protected $rate;

    /**
     * @var array[]
     */
    protected $limits;

    /**
     * SlidingWindow constructor.
     * @param int $rate
     */
    public function __construct(int $rate)
    {
        $this->rate = $rate;
    }

    /**
     * @param float $currentTime
     * @return bool
     */
    public function canDoWork(float $currentTime): bool
    {
        $time = (int) $currentTime;

        if (!isset($this->limits[$time])) {
            unset($this->limits[$time - 2]);
            $this->limits[$time] = 0;
        }

        $percent = 1 - ($currentTime - $time);
        $previous = 0;

        if (isset($this->limits[$time - 1])) {
            $previous = $this->limits[$time - 1] * $percent;
        }

        if (($this->limits[$time] + $previous) <= $this->rate) {
            $this->limits[$time]++;
            return true;
        }

        return false;
    }

}
