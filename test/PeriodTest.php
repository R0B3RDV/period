<?php

use League\Period\Period;

class PeriodTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $period = new Period('2014-05-01', '2014-05-08');
        $this->assertEquals(new DateTime('2014-05-01'), $period->getStart());
        $this->assertEquals(new DateTime('2014-05-08'), $period->getEnd());
        $this->assertInstanceof('League\Period\Interfaces\TimeRange', $period);
        $this->assertInstanceof('League\Period\Interfaces\TimeRangeInfo', $period);
        $this->assertInstanceof('League\Period\Interfaces\TimeRangeObject', $period);
    }

    /**
     * @expectedException \LogicException
     */
    public function testConstructorThrowException()
    {
        new Period(
            new DateTime('2014-05-01', new DateTimeZone('Europe/Paris')),
            new DateTime('2014-05-01', new DateTimeZone('Africa/Nairobi'))
        );
    }

    /**
     * @requires PHP 5.5
     */
    public function testConstructorWithDateTimeInterface()
    {
        $start = new DateTimeImmutable('2014-05-01');
        $end = new DateTimeImmutable('2014-05-08');
        $period = new Period($start, $end);
        $this->assertInstanceof('DateTimeInterface', $period->getStart());
        $this->assertInstanceof('DateTimeImmutable', $period->getStart());
        $this->assertEquals($end, $period->getEnd());
    }

    public function testCreateFromDurationWithDateTime()
    {
        $start = new DateTime();
        $period = Period::createFromDuration($start, "1 DAY");
        $this->assertEquals($period->getStart(), $start);
        $this->assertEquals($period->getEnd(), $start->add(new DateInterval('P1D')));
    }

    public function testCreateFromDurationWithString()
    {
        $start =  new DateTime('-1 DAY');
        $ttl = new DateInterval('P2D');
        $end = clone $start;
        $end->add($ttl);
        $period = Period::createFromDuration("-1 DAY", $ttl);
        $this->assertEquals($period->getStart(), $start);
        $this->assertEquals($period->getEnd(), $end);
    }

    public function testCreatFromDurationWithInteger()
    {
        $period = Period::createFromDuration('2014-01-01', 3600);
        $this->assertEquals(new DateTime('2014-01-01 01:00:00'), $period->getEnd());
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateFromDurationWithInvalidInteger()
    {
        Period::createFromDuration('2014-01-01', -1);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCreateFromDurationFailedWithOutofRangeInterval()
    {
        Period::createFromDuration(new DateTime(), "-1 DAY");
    }

    public function testCreateFromDurationBeforeEndWithDateTime()
    {
        $end = new DateTime();
        $period = Period::createFromDurationBeforeEnd($end, "1 DAY");
        $this->assertEquals($period->getEnd(), $end);
        $this->assertEquals($period->getStart(), $end->sub(new DateInterval('P1D')));
    }

    public function testCreateFromDurationBeforeEndWithString()
    {
        $end =  new DateTime('-1 DAY');
        $ttl = new DateInterval('P2D');
        $start = clone $end;
        $start->sub($ttl);
        $period = Period::createFromDurationBeforeEnd("-1 DAY", $ttl);
        $this->assertEquals($period->getStart(), $start);
        $this->assertEquals($period->getEnd(), $end);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCreateFromDurationBeforeEndFailedWithOutofRangeInterval()
    {
        Period::createFromDurationBeforeEnd(new DateTime(), "-1 DAY");
    }

    public function testCreateFromWeek()
    {
        $period = Period::createFromWeek(2014, 3);
        $this->assertEquals($period->getStart(), new DateTime('2014-01-13'));
        $this->assertEquals($period->getEnd(), new DateTime('2014-01-20'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromWeekFailedWithInvalidYear()
    {
        Period::createFromWeek("toto", 5);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromWeekFailedWithLowInvalidIndex()
    {
        Period::createFromWeek(2014, 0);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromWeekFailedWithHighInvalidIndex()
    {
        Period::createFromWeek(2014, 54);
    }

    public function testCreateFromMonth()
    {
        $period = Period::createFromMonth(2014, 3);
        $this->assertEquals($period->getStart(), new DateTime('2014-03-01'));
        $this->assertEquals($period->getEnd(), new DateTime('2014-04-01'));
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromMonthFailedWithHighInvalidIndex()
    {
        Period::createFromMonth(2014, 13);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromMonthFailedWithLowInvalidIndex()
    {
        Period::createFromMonth(2014, 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromMonthFailedWithInvalidYear()
    {
        Period::createFromMonth("toto", 8);
    }

    public function testCreateFromQuarter()
    {
        $period = Period::createFromQuarter(2014, 3);
        $this->assertEquals($period->getStart(), new DateTime('2014-07-01'));
        $this->assertEquals($period->getEnd(), new DateTime('2014-10-01'));
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromQuarterFailedWithHighInvalidIndex()
    {
        Period::createFromQuarter(2014, 5);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromQuarterFailedWithLowInvalidIndex()
    {
        Period::createFromQuarter(2014, 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromQuarterFailedWithInvalidYear()
    {
        Period::createFromQuarter("toto", 2);
    }

    public function testCreateFromSemester()
    {
        $period = Period::createFromSemester(2014, 2);
        $this->assertEquals($period->getStart(), new DateTime('2014-07-01'));
        $this->assertEquals($period->getEnd(), new DateTime('2015-01-01'));
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromSemesterFailedWithLowInvalidIndex()
    {
        Period::createFromSemester(2014, 0);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testCreateFromSemesterFailedWithHighInvalidIndex()
    {
        Period::createFromSemester(2014, 3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromSemesterFailedWithInvalidYear()
    {
        Period::createFromSemester("toto", 1);
    }

    public function testCreateFromYear()
    {
        $period = Period::createFromYear(2014);
        $this->assertEquals($period->getStart(), new DateTime('2014-01-01'));
        $this->assertEquals($period->getEnd(), new DateTime('2015-01-01'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromYearFailedWithInvalidYear()
    {
        Period::createFromYear("toto");
    }

    public function testCreateFromTimeRange()
    {
        $mock_timerange = $this->getMock('League\Period\Interfaces\TimeRange');
        $mock_timerange->expects($this->any())->method('getStart')->will($this->returnValue(new DateTime('-1 MONTH')));
        $mock_timerange->expects($this->any())->method('getEnd')->will($this->returnValue(new DateTime()));

        $res = Period::createFromTimeRange($mock_timerange);
        $this->assertInstanceof('League\Period\Period', $res);
    }
}
