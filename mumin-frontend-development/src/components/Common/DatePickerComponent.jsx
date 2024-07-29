import { useEffect, useRef } from "react";
import { LocalizationProvider } from "@mui/x-date-pickers";
import { DemoContainer, DemoItem } from "@mui/x-date-pickers/internals/demo";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { DatePicker } from "@mui/x-date-pickers/DatePicker";

const DatePickerComponent = ({
  className,
  date,
  handleDateChange,
  isCalenderOpen,
  openCalender,
  closeCalender,
  disabled,
  past
}) => {
  const inputRef = useRef(null);

  const handleRefClick = () => {
    openCalender();
  };

  useEffect(() => {
    if (inputRef.current) {
      const inputElement = inputRef.current;
      inputElement.addEventListener("click", handleRefClick);

      return () => {
        inputElement.removeEventListener("click", handleRefClick);
      };
    }
  }, [openCalender]);

  const handleCloseDatePicker = () => {
    closeCalender();
  };
  return (
    <LocalizationProvider dateAdapter={AdapterDayjs} id="parent">
      <DemoContainer id="check" components={["DatePicker"]}>
        <DemoItem>
          <div id="parent">
            <DatePicker
              open={isCalenderOpen}
              onOpen={openCalender}
              onClose={handleCloseDatePicker}
              className={className}
              value={date}
              onChange={handleDateChange}
              inputRef={inputRef}
              view="day"
              onAccept={closeCalender}
              disabled={disabled}
              disablePast={past}
            />
          </div>
        </DemoItem>
      </DemoContainer>
    </LocalizationProvider>
  );
};

export default DatePickerComponent;
