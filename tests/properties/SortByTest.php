<?php

class SortByTest extends BaseClass {

    public function testSortByRating() {
        $this->csv->sort_by = 'rating';
        $this->_compareWithExpected([
            // Rating 0
            'The Killing Kind',
            'The Third Secret',

            // Rating 3
            'The Last Templar',
            'The Broker (Paperback)',
            'Without Blood (Paperback)',

            // Rating 4
            'Deception Point (Paperback)',
            'The Rule of Four (Paperback)',
            'The Da Vinci Code (Hardcover)',

            // Rating 5
            'State of Fear (Paperback)',
            'Prey',
            'Digital Fortress : A Thriller (Mass Market Paperback)',
            'Angels & Demons (Mass Market Paperback)',
        ]);
    }

    public function testReverseSortByRating() {
        $this->csv->sort_by = 'rating';
        $this->csv->sort_reverse = true;
        $this->_compareWithExpected([

            // Rating 5
            'Digital Fortress : A Thriller (Mass Market Paperback)',
            'Prey',
            'State of Fear (Paperback)',
            'Angels & Demons (Mass Market Paperback)',

            // Rating 4
            'The Da Vinci Code (Hardcover)',
            'The Rule of Four (Paperback)',
            'Deception Point (Paperback)',

            // Rating 3
            'The Broker (Paperback)',
            'The Last Templar',
            'Without Blood (Paperback)',

            // Rating 0
            'The Third Secret',
            'The Killing Kind',
        ]);
    }
}
