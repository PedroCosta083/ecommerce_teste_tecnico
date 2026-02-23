import * as React from "react"
import { cn } from "@/lib/utils"

export interface MaskedInputProps
  extends React.InputHTMLAttributes<HTMLInputElement> {
  mask: string;
}

const MaskedInput = React.forwardRef<HTMLInputElement, MaskedInputProps>(
  ({ className, mask, value, onChange, ...props }, ref) => {
    const applyMask = (inputValue: string) => {
      if (!inputValue) return '';
      
      const numbers = inputValue.replace(/\D/g, '');
      let maskedValue = '';
      let numberIndex = 0;
      
      for (let i = 0; i < mask.length && numberIndex < numbers.length; i++) {
        if (mask[i] === '9') {
          maskedValue += numbers[numberIndex];
          numberIndex++;
        } else {
          maskedValue += mask[i];
        }
      }
      
      return maskedValue;
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
      const maskedValue = applyMask(e.target.value);
      const syntheticEvent = {
        ...e,
        target: { ...e.target, value: maskedValue }
      };
      onChange?.(syntheticEvent as React.ChangeEvent<HTMLInputElement>);
    };

    return (
      <input
        type="text"
        className={cn(
          "flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
          className
        )}
        ref={ref}
        value={value}
        onChange={handleChange}
        {...props}
      />
    )
  }
)
MaskedInput.displayName = "MaskedInput"

export { MaskedInput }
